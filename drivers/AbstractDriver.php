<?php
/**
 *
 */

namespace djeux\queue\drivers;


use djeux\queue\Queue;

abstract class AbstractDriver implements DriverInterface
{
    /**
     * @var Queue
     */
    protected $manager;

    public function __construct(Queue $manager)
    {
        $this->manager = $manager;

        $this->init();
    }

    protected function init()
    {

    }
}