<?php

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Dominikzogg\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Silex\KnpMenuServiceProvider;
use Knp\Menu\Silex\Voter\RouteVoter;
use Silex\Application;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Saxulum\AsseticTwig\Silex\Provider\AsseticTwigProvider;
use Saxulum\Console\Silex\Provider\ConsoleProvider;
use Saxulum\RouteController\Provider\RouteControllerProvider;
use Saxulum\Translation\Silex\Provider\TranslationProvider;
use Saxulum\SaxulumWebProfiler\Provider\SaxulumWebProfilerProvider;

// define the root dir
$rootDir = dirname(__DIR__);

// load composer
if (!$loader = @include $rootDir . '/vendor/autoload.php') {
    die("curl -s http://getcomposer.org/installer | php; php composer.phar install");
}

// annotation registry
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// create new silex app
$app = new Application();
$app['debug'] = getenv('APP_DEBUG') ? true : false;

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

$app->register(new ConsoleProvider());
$app->register(new DoctrineOrmServiceProvider());
$app->register(new DoctrineOrmManagerRegistryProvider());
$app->register(new KnpMenuServiceProvider());
$app->register(new AsseticTwigProvider());
$app->register(new RouteControllerProvider());
$app->register(new TranslationProvider());

$app['knp_menu.route.voter'] = $app->share(function (Application $app) {
    $voter = new RouteVoter();
    $voter->setRequest($app['request']);

    return $voter;
});

$app['knp_menu.matcher.configure'] = $app->protect(function (Matcher $matcher) use ($app) {
    $matcher->addVoter($app['knp_menu.route.voter']);
});

if ($app['debug']) {
    $app->register(new WebProfilerServiceProvider());
    $app->register(new SaxulumWebProfilerProvider());
}

// config overrides
$environment = getenv('APP_ENV') ?: 'prod';
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/config.yml", array('root_dir' => $rootDir, 'env' => $environment)));
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/config_{$environment}.yml", array('root_dir' => $rootDir)));
$app->register(new ConfigServiceProvider("{$rootDir}/app/config/parameters.yml"));

// load all project providers
$app->register(new \Vendor\Skeleton\SkeletonProvider());

// return the app
return $app;
