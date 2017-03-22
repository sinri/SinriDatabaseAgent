<?php
require_once(__DIR__.'/../autoload.php');

require_once(__DIR__.'/config.php');
require_once(__DIR__.'/TestBase.php');
require_once(__DIR__.'/TestPDO.php');
require_once(__DIR__.'/TestMySQLi.php');
use sinri\SinriDatabaseAgent\test\TestPDO;
use sinri\SinriDatabaseAgent\test\TestMySQLi;

date_default_timezone_set("Asia/Shanghai");

// PDO
echo "---- PDO ----".PHP_EOL;
$test_pdo=new TestPDO();
if(!$test_pdo->generalTest()){
    echo "TEST FAILED!".PHP_EOL;
    exit(1);
}

// MySQLi
echo "---- MySQLi ----".PHP_EOL;
$test_mysqli=new TestMySQLi();
if(!$test_mysqli->generalTest()){
    echo "TEST FAILED!".PHP_EOL;
    exit(1);
}

exit(0);
