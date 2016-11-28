<?php
/**
 *
 */

namespace djeux\queue\drivers;

use djeux\queue\jobs\BaseJob;
use Yii;
use yii\helpers\Json;

/**
 * Sync driver simply runs the process as soon as it is pushed,
 * mostly on development environments when you don't have an external queue manager (rabbitmq, redis, beanstalkd, etc)
 *
 * @package djeux\queue\drivers
 */
class SyncDriver extends AbstractDriver implements DriverInterface
{
    public function push($payload = '', $queue = '', $delay = 0)
    {
        $payload = Json::decode($payload);

        if (isset($payload['commandName'])) {
            $job = unserialize($payload['job']);
            $job->handle();
        }
    }

    public function pop($queue)
    {
        // nothing here
    }

    public function purge($queue)
    {
        // nothing here as well
    }

    public function delete(BaseJob $job)
    {
        // and here also
    }

    public function release(BaseJob $job)
    {
        // same here
    }
}