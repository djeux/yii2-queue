<?php
/**
 *
 */

namespace djeux\queue\exceptions;


use yii\base\Exception;

class InvalidHandlerException extends Exception
{
    public $message = "Handler must be defined in format of 'Class::method' or passed as an object";
}