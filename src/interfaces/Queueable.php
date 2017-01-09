<?php
/**
 *
 */

namespace djeux\queue\interfaces;


use djeux\queue\jobs\BaseJob;

interface Queueable
{
    public function handle(BaseJob $job);
}
