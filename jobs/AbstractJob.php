<?php
/**
 *
 */

namespace djeux\queue\jobs;

use djeux\queue\drivers\DriverInterface;
use yii\base\Object;
use Yii;

abstract class AbstractJob extends Object
{
    protected $driver;

    protected $driverJob;

    public function __construct(DriverInterface $driver, $driverJob)
    {
        parent::__construct();

        $this->driver = $driver;
        $this->driverJob = $driverJob;
    }

    /**
     * @return mixed
     */
    public function getDriverJob()
    {
        return $this->driverJob;
    }

    /**
     * @return mixed
     */
    public function process()
    {
        $data = $this->getData();

        list($id, $handler, $body) = $data;

        if (($pos = strpos('::', $handler)) > -1) {
            $class = substr($handler, 0, $pos);
            $method = substr($handler, $pos + 2);
        } else {
            $class = $handler;
            $method = 'handle';
        }

        $handler = Yii::createObject($class);
        return call_user_func_array([$handler, $method], [$this, $body]);
    }

    abstract public function delete();

    abstract public function getData();

    abstract public function release();

    abstract public function bury();
}