<?php

namespace Tws\Db;

use Tws\Job;

interface DbInterface
{
    public function startJob(Job $job);

    public function finishJob(Job $job);
}