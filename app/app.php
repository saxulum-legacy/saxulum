<?php

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
use Silex\Application;

// load composer
if (!$loader = @include dirname(__DIR__) . '/vendor/autoload.php')
{
    die("curl -s http://getcomposer.org/installer | php; php composer.phar install");
}

// annotation registry
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// base config vars
$arrConfig = array(
    'env' => getenv('APP_ENV') ?: 'prod',
    'root_dir' => dirname(__DIR__),
);

// php intl fallback
if (!function_exists('intl_get_error_code')) {
    require_once $arrConfig['root_dir'] . '/vendor/symfony/locale/Symfony/Component/Locale/Resources/stubs/functions.php';
    $loader->add('', $arrConfig['root_dir'] . '/vendor/symfony/locale/Symfony/Component/Locale/Resources/stubs');
}

// create new silex app
$app = new Application();

// load configs
$app->register(new ConfigServiceProvider("{$arrConfig['root_dir']}/app/config/config.yml", $arrConfig));
$app->register(new ConfigServiceProvider("{$arrConfig['root_dir']}/app/config/config_{$arrConfig['env']}.yml", $arrConfig));
$app->register(new ConfigServiceProvider("{$arrConfig['root_dir']}/app/config/parameters.yml"));

// register all provided silex providers
$app->register(new TwigServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new HttpCacheServiceProvider());
$app->register(new SecurityServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new MonologServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new DoctrineServiceProvider());
$app->register(new ServiceControllerServiceProvider());

// register usefull external providers
$app->register(new DoctrineOrmServiceProvider());

// load all project providers
require 'register.php';

// return the app
return $app;