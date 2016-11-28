<?php
/**
 *
 */

namespace djeux\queue\drivers;


use djeux\queue\jobs\BaseJob;

interface DriverInterface
{
    public function push($payload = '', $queue = '', $delay = 0);

    /**
     * @param string $queue
     * @return BaseJob
     */
    public function pop($queue);

    public function purge($queue);

    public function delete(BaseJob $job);

    public function release(BaseJob $job);
}