<?php
/**
 *
 */

namespace djeux\queue;


use djeux\queue\drivers\DriverInterface;
use djeux\queue\exceptions\InvalidHandlerException;
use yii\base\Component;
use Yii;
use yii\helpers\Json;

class Queue extends Component
{
    /**
     * Cache component or cache component id used in the queue component
     *
     * @var string
     */
    public $cache = 'cache';

    /**
     * Tube(channel) names that are used inside the queue component
     *
     * @var array
     */
    public $tubes = ['default'];

    /**
     * Driver that is used to handle the queue
     *
     * @var string|DriverInterface
     */
    public $driver = 'djeux\queue\drivers\SyncDriver';

    /**
     * @param string|object $handler
     * @param mixed $payload
     * @param string $queue
     * @param int $delay
     * @return string
     * @throws InvalidHandlerException
     */
    public function push($handler, $payload, $queue, $delay = 0)
    {
        $handler = $this->parseHandler($handler);

        $payload = Json::encode([
            'id' => $id = md5(uniqid('', true)),
            'handler' => $handler,
            'body' => $payload,
        ]);

        $this->getDriver()
            ->push($payload, $queue, $delay);

        return $id;
    }

    /**
     * @param string $queue
     * @return mixed
     */
    public function pop($queue)
    {
        return $this->getDriver()
            ->pop($queue);
    }

    public function purge($queue)
    {
        return $this->getDriver()
            ->purge($queue);
    }

    public function release($id)
    {

    }

    public function delete($id)
    {

    }

    /**
     * @return DriverInterface
     */
    protected function getDriver()
    {
        if (is_string($this->driver)) {
            $this->driver = Yii::createObject($this->driver, [$this]);
        }

        return $this->driver;
    }

    /**
     * @param string|object $handler
     * @return string
     * @throws InvalidHandlerException
     */
    protected function parseHandler($handler)
    {
        if (is_string($handler)) {
            if (!preg_match('/^[\w\\]+::[\w]+$', $handler)) {
                throw new InvalidHandlerException();
            }
        } elseif ($handler instanceof CallableQueueHandler) {
            $handler = get_class($handler);
        } else {
            throw new InvalidHandlerException();
        }

        return $handler;
    }
}