<?php

namespace Tws\Job;

use Tws\Db\DbInterface;
use Tws\Job;

class A extends Job
{
    public function __construct(DbInterface $db, $config)
    {
        $this->jobType = 'A';

        parent::__construct($db, $config);
    }
}