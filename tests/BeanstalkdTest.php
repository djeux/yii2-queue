<?php
/**
 *
 */

namespace tests;


use djeux\queue\drivers\BeanstalkdDriver;

class BeanstalkdTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockApplication([
            'components' => [
                'queue' => [
                    'driver' => [
                        'class' => 'djeux\queue\drivers\BeanstalkdDriver',
                        'host' => '127.0.0.1',
                    ],
                ],
            ],
        ]);
    }

    public function testConnection()
    {
        $beanstalkdDriver = \Yii::$app->get('queue')->getDriver();
        $this->assertInstanceOf('\djeux\queue\drivers\BeanstalkdDriver', $beanstalkdDriver);
        /* @var $beanstalkdDriver BeanstalkdDriver */
    }
}