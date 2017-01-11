<?php
/**
 *
 */

namespace tests;

use djeux\queue\interfaces\QueueManager;
use djeux\queue\Queue;
use djeux\queue\SyncQueueManager;
use tests\helpers\TestJob;
use Yii;

class SyncTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockApplication([
            'components' => [
                'queue' => [
                    'class' => SyncQueueManager::class,
                ],
            ],
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        $filename = __DIR__ . '/runtime/touch.txt';
        if (is_file($filename)) {
            unlink($filename);
        }
    }

    public function testObjectPush()
    {
        $queueComponent = $this->getQueue();

        $content = mt_rand(0, 1000) . 'new_content';
        $queueComponent->push(new TestJob($content), 'test_queue');

        // sync jobs are running right away, so we cannot pop and process them
        $this->assertFileExists(__DIR__ . '/runtime/touch.txt');
        $this->assertEquals($content, file_get_contents(__DIR__ . '/runtime/touch.txt'));
    }

    public function testCallablePush()
    {
        $queueComponent = $this->getQueue();

        $content = mt_rand(0, 1000) . 'new_content_callable';
        $queueComponent->push(TestJob::class . '@makeFile', $content, 'test_queue');

        $this->assertFileExists(__DIR__ . '/runtime/touch.txt');
        $this->assertEquals($content, file_get_contents(__DIR__ . '/runtime/touch.txt'));
    }

    /**
     * @return null|SyncQueueManager
     */
    private function getQueue()
    {
        return Yii::$app->get('queue');
    }
}