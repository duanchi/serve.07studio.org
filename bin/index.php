<?php
define('APPLICATION_PATH', dirname(dirname(__FILE__)));
define('PW_PATH', dirname(APPLICATION_PATH));

$application = new Yaf\Application(APPLICATION_PATH.'/conf/application.ini');
$application->bootstrap()->run();