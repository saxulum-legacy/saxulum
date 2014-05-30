<?php

namespace Vendor\Skeleton\Provider;

use Vendor\Skeleton\Menu\MenuBuilder;
use Silex\Application;
use Silex\ServiceProviderInterface;

class MenuProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['menu_builder'] = $app->share(function(Application $app){
            return new MenuBuilder($app['knp_menu.factory'], $app['translator']);
        });

        $app['main_menu'] = function(Application $app) {
            $menuBuilder = $app['menu_builder'];
            /** @var MenuBuilder $menuBuilder */
            return $menuBuilder->buildMenu($app['request']);
        };

        $knpMenuMenus = $app['knp_menu.menus'];
        $knpMenuMenus['main'] = 'main_menu';
        $app['knp_menu.menus'] = $knpMenuMenus;
    }

    public function boot(Application $app) {}
}