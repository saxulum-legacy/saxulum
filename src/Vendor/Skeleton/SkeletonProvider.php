<?php

namespace Vendor\Skeleton;

use Saxulum\SaxulumFramework\ControllerMap\ControllerMap;
use Saxulum\SaxulumFramework\ControllerMap\Controller;
use Saxulum\SaxulumFramework\ControllerMap\Method;
use Saxulum\SaxulumFramework\Provider\AbstractSilexBundleProvider;

class SkeletonProvider extends AbstractSilexBundleProvider
{
    /**
     * @return ControllerMap
     */
    protected function getControllerMap()
    {
        $controllerMap = parent::getControllerMap();

        $controller = new Controller();
        $controller->setServiceId("vendor.skeleton.controllerservice.testcontroller");
        $controller->setNamespace("Vendor\\Skeleton\\ControllerService\\TestController");
        $controller->setInjectionKeys(array('doctrine'));

        $method = new Method();
        $method->setName('setTwig');
        $method->setInjectionKeys(array('twig'));

        $controller->addMethod($method);

        $controllerMap->addController($controller);

        return $controllerMap;
    }
}
