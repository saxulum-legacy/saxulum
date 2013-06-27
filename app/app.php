<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Igorw\Silex\ConfigServiceProvider;
use Saxulum\SaxulumFramework\Provider\SaxulumServiceProvider;
use Silex\Application;

// define the root dir
$rootDir = dirname(__DIR__);

// load composer
if (!$loader = @include $rootDir . '/vendor/autoload.php') {
    die("curl -s http://getcomposer.org/installer | php; php composer.phar install");
}

// annotation registry
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// php intl fallback
if (!function_exists('intl_get_error_code')) {
    require_once $rootDir . '/vendor/symfony/locale/Symfony/Component/Locale/Resources/stubs/functions.php';
    $loader->add('', $rootDir . '/vendor/symfony/locale/Symfony/Component/Locale/Resources/stubs');
}

// create new silex app
$app = new Application();
$app['debug'] = getenv('APP_DEBUG') ? true : false;

// register all rewuired saxulum framework providers
$app->register(new SaxulumServiceProvider());

// config overrides
$environment = getenv('APP_ENV') ?: 'prod';
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/config.yml", array('root_dir' => $rootDir, 'env' => $environment)));
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/config_{$environment}.yml", array('root_dir' => $rootDir)));
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/parameters.yml"));

// load all project providers
$app->register(new \Vendor\Skeleton\SkeletonProvider());

// return the app
return $app;
