<?php
/**
 *
 */

namespace djeux\queue\drivers;

use Yii;
use yii\helpers\Json;

class SyncDriver implements DriverInterface
{
    public function push($payload = '', $queue = '', $delay = 0)
    {
        $data = Json::decode($payload);
        if (strpos($data['handler'], '::')) {
            list($class, $method) = explode('::', $data['handler']);
            $object = Yii::createObject($class);
        } else {
            $object = Yii::createObject($data['handler']);
            $method = 'handle';
        }

        call_user_func_array([$object, $method], $data);
    }

    public function pop($queue)
    {
        return null;
    }

    public function purge($queue)
    {
        return null;
    }

}