<?php
/**
 *
 */

namespace djeux\queue\jobs;


use djeux\queue\interfaces\QueueManager;
use yii\di\Instance;

class SyncJob extends BaseJob
{
    private $payload;

    private $queue;

    private $instance;

    public function __construct($payload, $queue = 'default')
    {
        $this->payload = $payload;
        $this->queue = $queue;
    }

    /**
     * @param string $class
     * @return object
     */
    protected function resolve($class)
    {
        return Instance::ensure($class);
    }

    /**
     * @param string $job
     * @return array
     */
    protected function parseJob($job)
    {
        $segments = explode('@', $job);

        return count($segments) > 1 ? $segments : [$segments[0], 'fire'];
    }

    public function delete()
    {
        //
    }

    public function getData()
    {
        return [];
    }

    public function release()
    {
        //
    }

    public function bury()
    {

    }

}