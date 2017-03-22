<?php
namespace sinri\SinriDatabaseAgent\test;

use sinri\SinriDatabaseAgent\SinriMySQLi;

/**
 *
 */
class TestMySQLi extends TestBase
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function connectDatabase()
    {
        $params=$this->config->forMySQLi();
        $this->db=(new SinriMySQLi($params));
    }
}
