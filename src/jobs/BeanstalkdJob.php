<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 09.01.2017
 * Time: 11:37
 */

namespace djeux\queue\jobs;


use djeux\queue\BeanstalkdQueueManager;
use djeux\queue\interfaces\QueueManager;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;

/**
 * Class BeanstalkdJob
 * @package djeux\queue\jobs
 *
 * @property BeanstalkdQueueManager $manager
 */
class BeanstalkdJob extends BaseJob
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var Pheanstalk
     */
    protected $pheanstalk;

    public function __construct(QueueManager $manager, $pheanstalk, $payload, $queue)
    {
        parent::__construct($manager, $payload, $queue);

        $this->pheanstalk = $pheanstalk;
    }

    public function getId()
    {
        return $this->job->getId();
    }

    /**
     * @return $this
     */
    public function bury()
    {
        $this->pheanstalk->bury($this->job);

        return $this;
    }

    /**
     * @return BaseJob
     */
    public function delete()
    {
        $this->pheanstalk->delete($this->job);

        return parent::delete();
    }

    /**
     * @return BaseJob
     */
    public function release()
    {
        $this->pheanstalk->release($this->job);

        return parent::release();
    }
}