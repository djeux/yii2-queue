<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 08.01.2017
 * Time: 18:19
 */

namespace djeux\queue;


use yii\base\Object;

class WorkerConfiguration extends Object
{
    public $listen = ['default'];

    public $stopCacheKey = 'yii2-queue:stop';

    public $restartCacheKey = 'yii2-queue:restart';
}