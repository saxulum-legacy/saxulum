<?php

putenv('APP_DEBUG=true');
putenv('APP_ENV=dev');

$app = require_once __DIR__.'/app/app.php';
/** @var Silex\Application $app */

$app->run();
