<?php
/**
 *
 */

namespace djeux\queue;

use Yii;
use yii\redis\Connection;

class RedisQueueManager extends BaseQueueManager
{
    /**
     * @var string|Connection
     */
    public $connection = 'redis';

    /**
     * @inheritDoc
     */
    public function push($job, $data = '', $queue = 'default')
    {
        $this->getConnection()->rpush($queue, $this->createPayload($job, $data));
    }

    /**
     * @inheritDoc
     */
    public function later($delay, $job, $data = '', $queue = null)
    {

    }

    /**
     * @inheritDoc
     */
    public function size($queue = 'default')
    {
        $connection = $this->getConnection();

        $command = <<<'LUA'
            return redis.call('llen', KEYS[1]) + redis.call('zcard', KEYS[2]) + redis.call('zcard', KEYS[3]) 
LUA;


        return $connection->eval($command, 3, $queue, $queue.':delayed', $queue.':reserved');
    }

    /**
     * @inheritDoc
     */
    public function pop($queue = 'default')
    {

    }

    /**
     * @return null|Connection
     */
    protected function getConnection()
    {
        if (is_string($this->connection)) {
            $this->connection = Yii::$app->get($this->connection);
        }

        return $this->connection;
    }
}