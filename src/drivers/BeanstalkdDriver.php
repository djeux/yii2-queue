<?php
/**
 *
 */

namespace djeux\queue\drivers;


use djeux\queue\jobs\BaseJob;
use djeux\queue\jobs\BeanstalkdJob;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Yii;

class BeanstalkdDriver extends AbstractDriver
{
    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * @var integer|null
     */
    public $port = Pheanstalk::DEFAULT_PORT;

    /**
     * @var integer|null
     */
    public $connectionTimeout;

    /**
     * @var boolean
     */
    public $connectionPersistent = false;

    /**
     * @var string
     */
    public $jobClass = 'djeux\queue\jobs\BeanstalkdJob';

    protected function init()
    {
        parent::init();
        $this->openConnection();
    }

    public function push($payload = '', $queue = '', $delay = Pheanstalk::DEFAULT_DELAY)
    {
        $this->pheanstalk->putInTube($queue, $payload, PheanstalkInterface::DEFAULT_PRIORITY, $delay);
    }

    /**
     * @param string $queue
     * @return null|BeanstalkdJob
     */
    public function pop($queue)
    {
        $job = $this->pheanstalk->reserveFromTube($queue, 0);

        if (false !== $job) {
            return new $this->jobClass($this, $job);
        }

        return null;
    }

    /**
     * @param string $queue
     */
    public function purge($queue)
    {
        $this->pheanstalk->watchOnly($queue);

        while ($job = $this->pheanstalk->reserve(0)) {
            $this->pheanstalk->delete($job);
        }
    }

    public function delete(BaseJob $job)
    {
        $this->pheanstalk->delete($job->getDriverJob());
    }

    public function release(BaseJob $job)
    {
        $this->pheanstalk->release($job->getDriverJob());
    }

    /**
     * @param BaseJob $job
     */
    public function bury(BaseJob $job)
    {
        $this->pheanstalk->bury($job->getDriverJob());
    }

    /**
     * @return Pheanstalk
     */
    protected function openConnection()
    {
        $this->pheanstalk = Yii::createObject(Pheanstalk::class, [
            $this->host,
            $this->port,
            $this->connectionTimeout,
            $this->connectionPersistent,
        ]);

        return $this->pheanstalk;
    }

}