<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 09.01.2017
 * Time: 11:45
 */

namespace djeux\queue\jobs;


class SyncJob extends BaseJob
{
    protected $job;

    public function bury()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getId()
    {
        return '';
    }
}