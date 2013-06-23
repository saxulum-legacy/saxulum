<?php

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

putenv('APP_DEBUG=true');
putenv('APP_ENV=dev');

$app = require_once __DIR__.'/app/app.php';
/** @var Silex\Application $app */

$app->run();
