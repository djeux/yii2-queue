<?php
/**
 *
 */

namespace tests\mock;


use Pheanstalk\Connection;
use Pheanstalk\Job;
use Pheanstalk\PheanstalkInterface;

class PheanstalkMock implements PheanstalkInterface
{
    /**
     * @var array
     */
    private $tubes = [];
    private $usingTube = 'default';
    private $watchedTubes = [];
    private $usedTubes = [];

    public function __construct($host, $port, $priority, $ttl)
    {

    }

    /**
     * @inheritDoc
     */
    public function setConnection(Connection $connection)
    {
        // TODO: Implement setConnection() method.
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        // TODO: Implement getConnection() method.
    }

    /**
     * @inheritDoc
     */
    public function bury($job, $priority = self::DEFAULT_PRIORITY)
    {
        // TODO: Implement bury() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($job)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function ignore($tube)
    {
        // TODO: Implement ignore() method.
    }

    /**
     * @inheritDoc
     */
    public function kick($max)
    {
        // TODO: Implement kick() method.
    }

    /**
     * @inheritDoc
     */
    public function kickJob($job)
    {
        // TODO: Implement kickJob() method.
    }

    /**
     * @inheritDoc
     */
    public function listTubes()
    {
        return array_keys($this->tubes);
    }

    /**
     * @inheritDoc
     */
    public function listTubesWatched($askServer = false)
    {
        return $this->watchedTubes;
    }

    /**
     * @inheritDoc
     */
    public function listTubeUsed($askServer = false)
    {
        // TODO: Implement listTubeUsed() method.
    }

    /**
     * @inheritDoc
     */
    public function pauseTube($tube, $delay)
    {
        // TODO: Implement pauseTube() method.
    }

    /**
     * @inheritDoc
     */
    public function resumeTube($tube)
    {
        // TODO: Implement resumeTube() method.
    }

    /**
     * @inheritDoc
     */
    public function peek($jobId)
    {
        // TODO: Implement peek() method.
    }

    /**
     * @inheritDoc
     */
    public function peekReady($tube = null)
    {
        // TODO: Implement peekReady() method.
    }

    /**
     * @inheritDoc
     */
    public function peekDelayed($tube = null)
    {
        // TODO: Implement peekDelayed() method.
    }

    /**
     * @inheritDoc
     */
    public function peekBuried($tube = null)
    {
        // TODO: Implement peekBuried() method.
    }

    /**
     * @inheritDoc
     */
    public function put($data, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY, $ttr = self::DEFAULT_TTR)
    {
        $this->tubes[$this->usingTube][] = $data;
    }

    /**
     * @inheritDoc
     */
    public function putInTube($tube, $data, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY, $ttr = self::DEFAULT_TTR)
    {
        $this->tubes[$tube][] = new Job(mt_rand(0,100000), $data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function release($job, $priority = self::DEFAULT_PRIORITY, $delay = self::DEFAULT_DELAY)
    {
        // TODO: Implement release() method.
    }

    /**
     * @inheritDoc
     */
    public function reserve($timeout = null)
    {
        $job = array_shift($this->tubes[$this->usingTube]);

        return $job;
    }

    /**
     * @inheritDoc
     */
    public function reserveFromTube($tube, $timeout = null)
    {
        $this->usingTube = $tube;
        return $this->reserve();
    }

    /**
     * @inheritDoc
     */
    public function statsJob($job)
    {
        // TODO: Implement statsJob() method.
    }

    /**
     * @inheritDoc
     */
    public function statsTube($tube)
    {
        // TODO: Implement statsTube() method.
    }

    /**
     * @inheritDoc
     */
    public function stats()
    {
        // TODO: Implement stats() method.
    }

    /**
     * @inheritDoc
     */
    public function touch($job)
    {
        // TODO: Implement touch() method.
    }

    /**
     * @inheritDoc
     */
    public function useTube($tube)
    {
        $this->usingTube = $tube;
    }

    /**
     * @inheritDoc
     */
    public function watch($tube)
    {
        // TODO: Implement watch() method.
    }

    /**
     * @inheritDoc
     */
    public function watchOnly($tube)
    {
        // TODO: Implement watchOnly() method.
    }

}