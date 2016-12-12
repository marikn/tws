<?php

namespace Tws;

use Tws\Db\DbInterface;

abstract class Job
{
    public $jobType;

    protected $config;
    protected $db;

    public function __construct(DbInterface $db, $config)
    {
        $this->db     = $db;
        $this->config = $config;
    }

    public function startJob()
    {
        return $this->db->startJob($this);
    }

    public function finishJob()
    {
        return $this->db->finishJob($this);
    }
}