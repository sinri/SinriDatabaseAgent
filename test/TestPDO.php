<?php
namespace sinri\SinriDatabaseAgent\test;

use sinri\SinriDatabaseAgent\SinriPDO;

/**
*
*/
class TestPDO extends TestBase
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function connectDatabase()
    {
        $params=$this->config->forPDO();
        $this->db=(new SinriPDO($params));
    }
}
