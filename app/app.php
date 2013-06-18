<?php

use Application\Provider\AdvancedKnpMenuServiceProvider;
use Dominikzogg\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\Application;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;

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

// register all provided silex providers
$app->register(new TranslationServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new HttpCacheServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new MonologServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new ServiceControllerServiceProvider());

// register usefull external providers
$app->register(new DoctrineOrmServiceProvider());
$app->register(new DoctrineOrmManagerRegistryProvider());
$app->register(new AdvancedKnpMenuServiceProvider());

if($app['debug']) {
    $app->register(new WebProfilerServiceProvider());
}

// add form extension
$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions, $app) {
    $extensions[] = new DoctrineOrmExtension($app['doctrine']);

    return $extensions;
}));

// config overrides
$environment = getenv('APP_ENV') ?: 'prod';
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/config.yml", array('root_dir' => $rootDir, 'env' => $environment)));
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/config_{$environment}.yml", array('root_dir' => $rootDir)));
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/parameters.yml"));

// load all project providers
require 'registerprovider.php';

// return the app
return $app;
