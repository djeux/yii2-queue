<?php
/**
 *
 */

namespace djeux\queue;


use djeux\queue\interfaces\QueueManager;
use djeux\queue\jobs\SyncJob;
use Yii;

class SyncQueueManager extends BaseQueueManager implements QueueManager
{
    /**
     * @var string
     */
    public $jobClass = 'djeux\queue\jobs\SyncJob';

    /**
     * @inheritDoc
     */
    public function push($job, $data = '', $queue = null)
    {
        $queueJob = $this->resolveJob($this->createPayload($job, $data), $queue);

        try {
            $queueJob->handle();
        } catch (\Exception $e) {
            \Yii::error($e->getMessage(), 'queue/sync');
        }
    }

    /**
     * @inheritDoc
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * @inheritDoc
     */
    public function size($queue = 'default')
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function pop($queue = 'default')
    {
        return null;
    }

    /**
     * @param mixed $payload
     * @param string $queue
     * @return object|SyncJob
     */
    protected function resolveJob($payload, $queue)
    {
        return Yii::createObject($this->jobClass, [$this, $payload, $queue]);
    }
}