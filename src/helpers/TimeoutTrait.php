<?php

namespace djeux\queue\helpers;


use yii\db\Connection;
use yii\db\Exception;

trait TimeoutTrait
{
    /**
     * Check whether connection is alive and the server has not "gone away"
     * Supports mysql for now
     *
     * @param Connection $db
     * @throws Exception
     */
    public function dbKeepAlive(Connection $db)
    {
        try {
            $db->createCommand('DO 1')->execute();
        } catch (Exception $e) {
            if ($this->causedByLostConnection($e)) {
                $db->close(); $db->open();
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param Exception $e
     * @return bool
     */
    private function causedByLostConnection(Exception $e)
    {
        $message = $e->getMessage();

        $needles = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
        ];

        foreach ($needles as $needle) {
            if (mb_strpos($message, $needle)) {
                return true;
            }
        }

        return false;
    }
}