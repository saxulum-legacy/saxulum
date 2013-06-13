<?php

namespace Application\Provider;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Silex\KnpMenuServiceProvider;
use Knp\Menu\Silex\Voter\RouteVoter;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Twig\MenuExtension;
use Silex\Application;
use Silex\ServiceProviderInterface;

class AdvancedKnpMenuServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app) {}

    public function register(Application $app)
    {
        $app->register(new KnpMenuServiceProvider());

        $app['knp_menu.route.voter'] = $app->share(function (Application $app) {
            $voter = new RouteVoter();
            $voter->setRequest($app['request']);

            return $voter;
        });

        $app['knp_menu.matcher.configure'] = $app->protect(function (Matcher $matcher) use ($app) {
            $matcher->addVoter($app['knp_menu.route.voter']);
        });

        $app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) use ($app) {
            $twig->addExtension(new MenuExtension(new Helper(
                $app['knp_menu.renderer_provider'],
                $app['knp_menu.menu_provider']))
            );

            return $twig;
        }));
    }
}
