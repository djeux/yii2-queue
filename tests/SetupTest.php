<?php
/**
 *
 */

namespace tests;


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

        $this->assertInstanceOf('djeux\queue\Queue', $component);
    }
}