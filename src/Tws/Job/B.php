<?php

namespace Tws\Job;

use Tws\Db\DbInterface;
use Tws\Job;

class B extends Job
{
    public function __construct(DbInterface $db, $config)
    {
        $this->jobType = 'B';

        parent::__construct($db, $config);
    }
}