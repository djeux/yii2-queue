<?php
/**
 *
 */

namespace tests;


use djeux\queue\interfaces\QueueManager;

class SetupTest extends TestCase
{
    protected function setUp()
    {
        $this->mockApplication();
        parent::setUp();
    }

    public function testComponent()
    {
        $this->assertTrue(\Yii::$app->has('queue'));
        $component = \Yii::$app->get('queue');

        $this->assertInstanceOf(QueueManager::class, $component);
    }
}