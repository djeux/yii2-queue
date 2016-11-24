<?php
/**
 *
 */

namespace djeux\queue\drivers;


use djeux\queue\jobs\AbstractJob;

interface DriverInterface
{
    public function push($payload = '', $queue = '', $delay = 0);

    public function pop($queue);

    public function purge($queue);

    public function delete(AbstractJob $job);

    public function release(AbstractJob $job);
}