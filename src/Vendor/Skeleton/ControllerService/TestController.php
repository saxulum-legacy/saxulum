<?php

namespace Vendor\Skeleton\ControllerService;

use Dominikzogg\Doctrine\Registry\ManagerRegistry;
use Vendor\Skeleton\Entity\Example;
use Saxulum\SaxulumFramework\Controller\ControllerRouteInterface;
use Silex\Application;

class TestController implements ControllerRouteInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public static function addRoutes(Application $app, $serviceId)
    {
        $app
            ->match('/test', $serviceId . ':indexAction')
            ->bind('test_index')
        ;
    }

    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param  \Twig_Environment $twig
     * @return TestController
     */
    public function setTwig($twig)
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->twig;
    }

    /**
     * @return string
     */
    public function indexAction()
    {
        $examples = $this->getDoctrine()->getManager()->getRepository(get_class(new Example()))->findAll();

        return $this->getTwig()->render('@VendorSkeleton/Example/list.html.twig', array(
            'examples' => $examples,
        ));
    }
}
