<?php

namespace Tws\Controller;

use Tws\Db\MySQLAdapter;
use Tws\Job;

class APIController
{
    protected $config;
    protected $db;

    public function __construct($config)
    {
        $this->config = $config;
        switch ($config['db_adapter']) {
            case 'mysql':
                $this->db  = new MySQLAdapter($this->config);
                break;
            default:
                $this->db  = new MySQLAdapter($this->config);
                break;
        }
    }

    public function obtainExecutionLockAction($job)
    {
        $className = "Tws\\Job\\$job";
        /**
         * @var Job $jobObject
         */
        $jobObject = new $className($this->db, $this->config);
        if ($jobObject->startJob() > 0) {
            return json_encode(['code' => 0, 'message' => 'OK']);
        }

        return json_encode(['code' => 0, 'message' => 'FAIL']);
    }

    public function releaseExecutionLockAction($job)
    {
        $className = "Tws\\Job\\$job";
        /**
         * @var Job $jobObject
         */
        $jobObject = new $className($this->db, $this->config);
        $jobObject->finishJob();

        return json_encode(['code' => 0, 'message' => 'OK']);
    }
}