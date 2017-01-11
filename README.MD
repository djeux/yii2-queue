##General
Queue component for yii2

Currently only supports beanstalkd driver but can easily be extended to support other types

##Installation
```ini
composer require djeux/yii2-queue
```

##Configuration
In your config/main.php (because you need to setup the queue to work in both console and web)
```php
'components' => [
    'queue' => [
        'class' => 'djeux\queue\BeanstalkdQueueManager', // in this example we use beanstalkd driver
        'host' => '127.0.0.1',
        'worker' => [
            'listen' => ['default', /* here you define what tubes should the worker listen to */]
        ],
    ],
]
```

##Usage
In your code you can push jobs two ways
1. By passing an object which implements 'Queueable' interface
```php
Yii::$app->queue->push(new MyJob("text to push"), '', 'default');

class MyJob implements Queueable
{   
    protected $pushedText;
    
    public function __construct($text)
    {
        $this->pushedText = $text;
    }
    
    /**
     * @param $job BaseJob
     */
    public function handle($job)
    {
        echo $this->pushedText;
        $job->delete();
    }
}
```
2. By passing a string with class and method separated by '@'
```php
Yii::$app->queue->push('app\components\queue\MyJob@myMethod', 'text to push', 'default');

class MyJob
{   
    public function myMethod($job, $data)
    {
        echo $data;
        $job->delete();
    }
}
```

### Workers
You can setup workers to run from supervisord

There are two main methods
```ini
php yii queue/manager
```
Runs a manager which checks the configured worker and launched a process for each listed queue

```ini
php yii queue/worker <queue>
```
Which runs a daemon that processes queue jobs

## Troubleshooting
If you have jobs that use database connection, and they're running in daemon mode, the server will eventually disconnect you. @see wait_timeout

To counter that there's a trait in \djeux\queue\helpers\TimeoutTrait which you can call to keep the jobs processing and reconnect if the connection is dropped

Same goes for swiftmailer if you sending emails. The smtp will eventually disconnect you with 
```ini
421 Timeout waiting for data from client.
```
To counter that you can use 
```php
try {
    \Yii::$app->mailer->send($message);
} catch (\Exception $e) {
    if (strpos(strtolower($e->getMessage()), '421 timeout') !== false) {
        if (($mailer = \Yii::$app->mailer) instanceof Mailer) {
            /* @var $mailer Mailer */
            $mailer->getSwiftMailer()->getTransport()->stop();
            $this->send($job, $data);
        }
    }
}
```

You can add methods like "beforeJob" and "afterJob" which will be called before and after a job is processed.

#P.S.
Package inspiration taken from Laravel implementation of queue system.

For any suggestions, fixes and hate posts, please write issues.