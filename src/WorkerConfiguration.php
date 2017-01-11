<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 08.01.2017
 * Time: 18:19
 */

namespace djeux\queue;


use yii\base\Object;

/**
 * Class WorkerConfiguration
 * Object used to store configuration for the console workers
 *
 * @package djeux\queue
 */
class WorkerConfiguration extends Object
{
    /**
     * List of tubes/channels/etc that the console worker should listen to
     * queue/manager will create a queue/work process for each entry from this list
     *
     * @var array
     */
    public $listen = ['default'];

    /**
     * Cache key that is used to the the stop marker for the console worker
     *
     * @var string
     */
    public $stopCacheKey = 'yii2-queue:stop';

    /**
     * Cache key that is used to store the restart marker for the console worker
     *
     * @var string
     */
    public $restartCacheKey = 'yii2-queue:restart';
}