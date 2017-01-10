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
use Pheanstalk\PheanstalkInterface;

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

    /**
     * BeanstalkdJob constructor.
     * @param QueueManager $manager
     * @param PheanstalkInterface $pheanstalk
     * @param Job $job
     * @param string $queue
     */
    public function __construct(QueueManager $manager, PheanstalkInterface $pheanstalk, Job $job, $queue)
    {
        parent::__construct($manager, $job->getData(), $queue);

        $this->pheanstalk = $pheanstalk;
        $this->job = $job;
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