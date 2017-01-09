<?php


namespace djeux\queue;

use djeux\queue\interfaces\Queueable;
use djeux\queue\jobs\BaseJob;

class CalledQueueHandler
{
    public function call(BaseJob $job, $data = [])
    {
        /* @var $command Queueable */
        $command = unserialize($data['command']);

        $command->handle($job);

        if (!$job->isReleasedOrDeleted()) {
            $job->delete();
        }
    }
}