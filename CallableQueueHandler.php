<?php
/**
 *
 */

namespace djeux\queue;


interface CallableQueueHandler
{
    public function handle($data = null);
}