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
 * @property BeanstalkdDriver $manager
 * @property Job $driverJob
 */
class BeanstalkdJob extends BaseJob
{
    public function delete()
    {
        $this->manager->delete($this);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->driverJob->getId();
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
        return $this->manager->release($this);
    }

    public function bury()
    {
        return $this->manager->bury($this);
    }
}