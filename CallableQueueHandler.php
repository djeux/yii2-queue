<?php
/**
 *
 */

namespace djeux\queue;


use djeux\queue\jobs\AbstractJob;

interface CallableQueueHandler
{
    public function handle(AbstractJob $job, $data = null);
}