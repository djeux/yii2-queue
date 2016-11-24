<?php
/**
 *
 */

namespace djeux\queue\drivers;


use djeux\queue\jobs\AbstractJob;
use djeux\queue\jobs\BeanstalkdJob;
use djeux\queue\Queue;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

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

    public function delete(AbstractJob $job)
    {
        $this->pheanstalk->delete($job->getDriverJob());
    }

    public function release(AbstractJob $job)
    {
        $this->pheanstalk->release($job->getDriverJob());
    }

    /**
     * @return Pheanstalk
     */
    protected function openConnection()
    {
        $this->pheanstalk = new Pheanstalk(
            $this->host,
            $this->port,
            $this->connectionTimeout,
            $this->connectionPersistent
        );

        return $this->pheanstalk;
    }

}