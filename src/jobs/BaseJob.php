<?php
/**
 *
 */

namespace djeux\queue\jobs;

use djeux\queue\drivers\DriverInterface;
use djeux\queue\interfaces\Queueable;
use djeux\queue\interfaces\QueueManager;
use yii\base\Object;
use Yii;
use yii\di\Instance;
use yii\helpers\Json;

abstract class BaseJob extends Object
{
    /**
     * @var QueueManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $queue;

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * @var Queueable
     */
    protected $instance;

    /**
     * @var bool
     */
    protected $deleted = false;

    /**
     * @var bool
     */
    protected $released = false;

    public function __construct(QueueManager $manager, $payload, $queue)
    {
        parent::__construct();

        $this->payload = $payload;
        $this->manager = $manager;
        $this->queue = $queue;
    }

    public function handle()
    {
        $payload = $this->payload();

        list($class, $method) = $this->parseJob($payload['job']);

        $this->instance = $this->resolve($class);

        $this->instance->{$method}($this, $payload['data']);
    }

    /**
     * Parse the job name and return the called class and method
     *
     * @param string $job
     * @return array [class, method]
     */
    protected function parseJob($job)
    {
        $segments = explode('@', $job);

        return count($segments) == 2 ? $segments : [$segments[0], 'handle'];
    }

    /**
     * Fetch the payload
     *
     * @return mixed
     */
    public function payload()
    {
        return Json::decode($this->payload);
    }

    /**
     * @param string $class
     * @return object
     */
    protected function resolve($class)
    {
        return Yii::createObject(['class' => $class]);
    }

    /**
     * @return $this
     */
    public function delete()
    {
        $this->deleted = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function release()
    {
        $this->released = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReleasedOrDeleted()
    {
        return $this->deleted || $this->released;
    }

    /**
     * Unique identifier of the job
     *
     * @return mixed
     */
    abstract public function getId();

    abstract public function bury();
}