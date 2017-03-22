<?php
namespace sinri\SinriDatabaseAgent\test;

/**
*
*/
class TestDatabaseConfig
{
    private $host="127.0.0.1";
    private $port=3306;
    private $username='root';
    private $password='123456';
    private $database='test';
    private $charset='utf8';
    
    public function __construct()
    {
        # code...
    }

    public function forPDO()
    {
        return [
            "host"=>$this->host,
            "port"=>$this->port,
            "username"=>$this->username,
            "password"=>$this->password,
            "database"=>$this->database,
            "charset"=>$this->charset,
        ];
    }
    public function forMySQLi()
    {
        return [
            "host"=>$this->host,
            "port"=>$this->port,
            "username"=>$this->username,
            "password"=>$this->password,
            "database"=>$this->database,
            "charset"=>$this->charset,
        ];
    }
}
