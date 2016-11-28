<?php
/**
 *
 */

namespace djeux\queue\jobs;

use djeux\queue\drivers\DriverInterface;
use djeux\queue\interfaces\QueueManager;
use yii\base\Object;
use Yii;

abstract class BaseJob extends Object
{
    protected $manager;

    protected $driverJob;

    protected $queue;

    public function __construct(QueueManager $manager, $driverJob, $queue)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->driverJob = $driverJob;
        $this->queue = $queue;
    }

    public function fire()
    {
        $payload = $this->payload;

        list($class, $method) = $this->parseJob($payload['job']);

        $this->instance = $this->resolve($class);

        $this->instance->{$method}($this, $payload['data']);
    }

    /**
     * Unique identifier of the job
     *
     * @return string|integer
     */
    public function getId()
    {
        return $this->getData()['id'];
    }

    /**
     * @return mixed
     */
    public function getDriverJob()
    {
        return $this->driverJob;
    }

    /**
     * @return mixed
     */
    public function process()
    {
        $data = $this->getData();

        $handler = $this->resolveName($data);
    }

    /**
     * @param array $data
     */
    protected function resolveName(array $data)
    {

    }

    abstract public function delete();

    abstract public function getData();

    abstract public function release();

    abstract public function bury();
}