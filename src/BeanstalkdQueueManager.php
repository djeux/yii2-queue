<?php
/**
 *
 */

namespace djeux\queue;


use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Yii;

class BeanstalkdQueueManager extends BaseQueueManager
{
    /**
     * @var string
     */
    public $host;

    /**
     * @var integer
     */
    public $port = PheanstalkInterface::DEFAULT_PORT;

    /**
     * @var integer|null
     */
    public $connectionTimeout;

    /**
     * @var boolean
     */
    public $connectPersistent = false;

    /**
     * @var string
     */
    public $jobClass = 'djeux\queue\jobs\BeanstalkdJob';

    /**
     * Time to run
     *
     * @var int
     */
    public $ttr = PheanstalkInterface::DEFAULT_TTR;

    /**
     * @var PheanstalkInterface
     */
    protected $pheanstalk;

    /**
     * @inheritDoc
     */
    public function push($job, $data = '', $queue = null)
    {
        $payload = $this->createPayload($job, $data);

        return $this->getPheanstalk()
            ->putInTube($queue, $payload, PheanstalkInterface::DEFAULT_PRIORITY, PheanstalkInterface::DEFAULT_DELAY, $this->ttr);
    }

    /**
     * @inheritDoc
     */
    public function later($delay, $job, $data = '', $queue = 'default')
    {
        $payload = $this->createPayload($job, $data);

        return $this->getPheanstalk()
            ->putInTube($queue, $payload, PheanstalkInterface::DEFAULT_PRIORITY, $delay, $this->ttr);
    }

    /**
     * @inheritDoc
     */
    public function size($queue = 'default')
    {
        return (int) $this->getPheanstalk()->statsTube($queue)->total_jobs;
    }

    /**
     * @inheritDoc
     */
    public function pop($queue = 'default')
    {
        if ($job = $this->getPheanstalk()->reserveFromTube($queue, 0)) {
            return Yii::createObject($this->jobClass, [$this, $this->pheanstalk, $job, $queue]);
        }

        return null;
    }

    /**
     * @return object|PheanstalkInterface
     */
    public function getPheanstalk()
    {
        if (!$this->pheanstalk) {
            $this->pheanstalk = Yii::createObject(Pheanstalk::class, [
                $this->host,
                $this->port,
                $this->connectionTimeout,
                $this->connectPersistent,
            ]);
        }

        return $this->pheanstalk;
    }
}