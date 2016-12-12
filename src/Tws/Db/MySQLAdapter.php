<?php

namespace Tws\Db;

use Tws\Job;

class MySQLAdapter implements DbInterface
{
    private $config;
    private $db;

    public function __construct($config)
    {
        $this->config = $config;
        $this->db = new \PDO('mysql:dbname=' . $config['mysql']['db'] . ';host=' . $config['mysql']['host'], $config['mysql']['user'], $config['mysql']['pass'], [\PDO::ATTR_PERSISTENT => true]);
    }

    public function startJob(Job $job)
    {
        $limit = $this->config['job'][$job->jobType];

        $sth = $this->db->prepare("update throttling as t inner join job_types as jt on jt.id = t.job set count = count + 1 where jt.name = '$job->jobType' and t.count < $limit");
        $sth->execute();

        return $sth->rowCount();
    }

    public function finishJob(Job $job)
    {
        $sth = $this->db->prepare("update throttling as t inner join job_types as jt on jt.id = t.job set count = count - 1 where jt.name = '$job->jobType' and count > 0");

        return $sth->execute();
    }
}