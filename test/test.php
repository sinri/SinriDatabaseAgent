<?php
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/../src/SinriDatabaseAgent.php');
require_once(__DIR__.'/../src/SinriPDO.php');
require_once(__DIR__.'/TestBase.php');
require_once(__DIR__.'/TestPDO.php');
use sinri\SinriDatabaseAgent\test\TestPDO;

date_default_timezone_set("Asia/Shanghai");

// PDO
$test_pdo=new TestPDO();
$test_pdo->generalTest();
