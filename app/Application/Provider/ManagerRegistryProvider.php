<?php

namespace Application\Provider;

use Application\Doctrine\Registry\ManagerRegistry;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ManagerRegistryProvider implements ServiceProviderInterface
{
    public function boot(Application $app) {}

    public function register(Application $app)
    {
        $app['doctrine'] = $app->share(function($app) {
            return new ManagerRegistry($app);
        });
    }
}