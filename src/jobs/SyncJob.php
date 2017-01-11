<?php

namespace djeux\queue\jobs;

/**
 * Class SyncJob
 * Sync jobs are launched as soon as they're pushed on to the queue
 *
 * @package djeux\queue\jobs
 */
class SyncJob extends BaseJob
{
    /**
     * @return string
     */
    public function bury()
    {
        return '';
    }

    /**
     * Sync jobs ID (does not have one)
     *
     * @return string
     */
    public function getId()
    {
        return '';
    }
}