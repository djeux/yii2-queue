<?php
/**
 *
 */

namespace djeux\queue;


use djeux\queue\interfaces\QueueManager;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\helpers\Json;
use Yii;

/**
 * Class BaseQueueManager
 * @property $cache \yii\caching\Cache
 *
 * @package djeux\queue
 */
abstract class BaseQueueManager extends Component implements QueueManager
{
    public $connection;

    /**
     * @var string
     */
    public $cache = 'cache';

    /**
     * Whether a job should be deleted by default (default: true)
     * If this is set to false, you must delete the job from withing it
     *
     * @var bool
     */
    public $deleteByDefault = true;

    public $workerConfig = [];

    /**
     * @var WorkerConfiguration
     */
    private $worker;

    public function init()
    {
        parent::init();

        $this->worker = new WorkerConfiguration($this->workerConfig);
    }

    /**
     * @return WorkerConfiguration
     */
    public function getWorkerConfiguration()
    {
        return $this->worker;
    }

    /**
     * @param string|object $job
     * @param mixed $data
     * @return string
     */
    protected function createPayload($job, $data)
    {
        if (is_object($job)) {
            $payload = Json::encode([
                'job' => 'djeux\queue\CalledQueueHandler@call',
                'data' => [
                    'commandName' => get_class($job),
                    'command' => serialize(clone $job),
                ],
            ]);
        } else {
            $payload = Json::encode($this->createPlainPayload($job, $data));
        }

        return $payload;
    }

    /**
     * @param string $job
     * @param mixed $data
     * @return array
     */
    protected function createPlainPayload($job, $data)
    {
        return ['job' => $job, 'data' => $data];
    }

    /**
     * Fetch the cache component used to store runtime information
     *
     * @return null|Cache
     * @throws InvalidConfigException
     */
    public function getCache()
    {
        if (is_string($this->cache)) { // component is not initialized
            $this->cache = Yii::$app->get($this->cache);
            if (!$this->cache instanceof Cache) {
                throw new InvalidConfigException("cache must be instance of yii\\caching\\Cache");
            }
        }

        return $this->cache;
    }
}