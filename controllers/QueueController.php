<?php

namespace djeux\queue\controllers;
use djeux\queue\Queue;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use yii\caching\Cache;
use yii\console\Controller;
use yii\helpers\Console;
use Yii;

/**
 * Handler for the queue processes
 */
class QueueController extends Controller
{
    /**
     * Cache key used to mark the RESTART request
     *
     * @var string
     */
    public $restartCacheKey = 'yii2-queue:restart';

    /**
     * Cache key used to mark the STOP request
     *
     * @var string
     */
    public $stopCacheKey = 'yii2-queue:stop';

    /**
     * @var Queue
     */
    private $queueApplicationComponent;

    private $commandPath;

    /**
     * @var string|Cache
     */
    private $cache;

    public function init()
    {
        parent::init();
        $this->commandPath = Yii::$app->basePath;

        if (!$this->cache instanceof Cache) {
            $this->cache = $this->getQueueComponent()->cache;
        }
    }

    /**
     * @return Queue|null
     */
    public function getQueueComponent()
    {
        if (!$this->queueApplicationComponent) {
            $this->queueApplicationComponent = Yii::$app->get('queue');
        }

        return $this->queueApplicationComponent;
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if ($action->id !== 'restore' && $this->isStopped()) {
            sleep(10); // check only every 10 secs
            $this->stdout("Queue is stopped", Console::FG_RED);
            return false;
        }

        return true;
    }

    /**
     * Run a listener for each tube
     *
     * @return integer
     * @throws \yii\base\InvalidConfigException
     */
    public function actionManager()
    {
        $queue = $this->getQueueComponent();

        if (!$queue) {
            $this->stderr("'queue' component undefined");
            return self::EXIT_CODE_ERROR;
        }

        $listenTubes = $queue->tubes;

        $runningProcesses = [];

        try {
            $this->stdout("Listening jobs from: " . implode(', ', $listenTubes), Console::FG_GREEN);
            foreach ($listenTubes as $tube) {
                $command = $this->createCommand($tube);
                $process = new Process($command, $this->commandPath);
                $this->stdout("Running worker for tube: {$tube}");
                $process->start([$this, 'handleOutput']);

                $runningProcesses[$tube] = $process;
            }
        } catch (ProcessFailedException $e) {
            $this->stderr($e->getMessage(), Console::FG_RED);
        }

        reset($runningProcesses);
        while (list($name, $runningProc) = each($runningProcesses)) {
            /* @var $runningProc Process */

            if (!$runningProc->isRunning()) {
                if ($this->isStopped() || $this->shouldRestart()) { // If the listener should restart, we remove all non running jobs
                    unset($runningProcesses[$name]);
                } else {
                    $runningProcesses[$name] = $runningProc->restart([$this, 'handleOutput']);
                    $this->stdout("Restarting worker for: {$name}");
                }
            }

            if ($runningProc == last($runningProcesses)) {
                reset($runningProcesses);
            }
            usleep(10000);
        }

        if ($this->shouldRestart()) {
            $this->getCache()->delete($this->restartCacheKey);
        }

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * @param string $type
     * @param string $line
     */
    public function handleOutput($type, $line)
    {
        if ($type === Process::ERR)
            $this->stderr($line);
        else
            $this->stdout($line);
    }

    /**
     * Create a command to run which listen to a tube
     *
     * @param string $tubeName
     * @return string
     */
    private function createCommand($tubeName)
    {
        return 'exec ' . PHP_BINARY . ' ' . Yii::$app->request->getScriptFile() . ' queue/work ' . $tubeName;
    }

    /**
     * Listen to a tube
     *
     * @param string $tubeName
     * @return int
     * @internal param string $queue
     */
    public function actionWork($tubeName)
    {
        $queue = $this->getQueueComponent();

        if (!in_array($tubeName, $queue->tubes)) {
            throw new \RuntimeException("Da fuck you're listening to? $tubeName");
        }

        $run = true;
        $failContainer = [];

        while ($run) {
            $job = $queue->pop($tubeName);

            if (null !== $job) {
                $jobId = $job->id;
                $fails = isset($failContainer[$jobId]) ? $failContainer[$jobId] : $failContainer[$jobId] = 0;

                try {
                    $job->process();

                    $job->delete();
                } catch (\Exception $e) {
                    $failContainer[$job->id] = $fails++;
                    $this->stderr($e->getMessage());
                    Yii::error($e->getMessage(), 'queue.' . $tubeName);

                    if ($fails > 5) {
                        $job->bury();
                    } else {
                        $job->release();
                    }
                }
            }

            if ($this->isStopped() || $this->shouldRestart()) {
                $run = false;
            }

            usleep(10000);
        }

        $this->stdout("Terminating on request");
        return self::EXIT_CODE_NORMAL;
    }

    /**
     * Restart the worker after all currently running jobs finish
     *
     * @return integer
     */
    public function actionRestart()
    {
        if ($this->getCache()->set($this->restartCacheKey, time())) {
            return self::EXIT_CODE_NORMAL;
        }

        $this->stderr("Unable to order restart");
        return self::EXIT_CODE_ERROR;
    }

    /**
     * Stop the queue workers from processing further jobs
     *
     * @return int
     */
    public function actionStop()
    {
        $this->getCache()->set($this->stopCacheKey, time());
        $this->stdout("Stopped");

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * @return boolean
     */
    public function shouldRestart()
    {
        if ($this->getCache()->exists($this->restartCacheKey)) {
            $this->stdout("Restarting worker");
            return true;
        }

        return false;
    }

    /**
     * Restore the queue to its default state
     *
     * @return boolean
     */
    public function actionRestore()
    {
        $this->getCache()->delete($this->stopCacheKey);
        $this->stdout("Restored");

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * Check whether the queue process should stop
     *
     * @return boolean
     */
    public function isStopped()
    {
        return $this->getCache()->exists($this->stopCacheKey);
    }

    /**
     * @return \yii\caching\Cache
     * @throws \yii\base\InvalidConfigException
     */
    protected function getCache()
    {
        if (is_string($this->cache) && Yii::$app) {
            $this->cache = Yii::$app->get($this->cache);
        }

        return $this->cache;
    }
}