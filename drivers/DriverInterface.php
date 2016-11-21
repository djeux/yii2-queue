<?php
/**
 *
 */

namespace djeux\queue\drivers;


interface DriverInterface
{
    public function push($payload = '', $queue = '', $delay = 0);

    public function pop($queue);

    public function purge($queue);
}