<?php
/**
 *
 */

namespace djeux\queue;


use djeux\queue\interfaces\QueueManager;
use yii\helpers\Json;

abstract class BaseQueueManager implements QueueManager
{
    public $connection;

    /**
     * @param string|object $job
     * @param mixed $data
     * @return string
     */
    protected function createPayload($job, $data)
    {
        if (is_object($job)) {
            $payload = Json::encode([
                'job' => 'djeux\queue\CallQueuedHandler@call',
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
}