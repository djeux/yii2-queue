<?php
/**
 *
 */

namespace djeux\queue\jobs;

use djeux\queue\drivers\BeanstalkdDriver;
use Pheanstalk\Job;
use yii\helpers\Json;

/**
 * Class BeanstalkdJob
 * @package djeux\queue\jobs
 *
 * @property BeanstalkdDriver $driver
 * @property Job $driverJob
 */
class BeanstalkdJob extends AbstractJob
{
    public function delete()
    {
        $this->driver->delete($this);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = $this->driverJob->getData();

        return Json::decode($data);
    }

    public function release()
    {
        // TODO: Implement release() method.
    }

    public function bury()
    {
        // TODO: Implement bury() method.
    }
}