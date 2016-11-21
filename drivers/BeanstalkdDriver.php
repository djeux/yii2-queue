<?php
/**
 *
 */

namespace djeux\queue\drivers;


class BeanstalkdDriver implements DriverInterface
{
    public function push($handler, $payload = '', $queue = '', $delay = 0)
    {

    }

    public function pop($queue)
    {
        // TODO: Implement pop() method.
    }

    public function purge($queue)
    {
        // TODO: Implement purge() method.
    }

}