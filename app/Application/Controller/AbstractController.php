<?php

namespace Application\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\HttpCache;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator;

abstract class AbstractController implements MountableControllerProviderInterface
{
    /**
     * @var \Pimple
     */
    protected $container;

    /**
     * @return string
     */
    abstract public function getMount();

    public function connect(Application $app)
    {
        $this->container = $app;
        return $this->addRoutes($this->getControllerFactory());
    }

    /**
     * @param ControllerCollection $controllerCollection
     * @return ControllerCollection
     */
    abstract protected function addRoutes(ControllerCollection $controllerCollection);

    /**
     * @return ControllerCollection
     */
    protected function getControllerFactory()
    {
        return $this->container['controllers_factory'];
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->container['twig'];
    }

    /**
     * @return UrlGenerator
     */
    protected function getUrlGenerator()
    {
        return $this->container['url_generator'];
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->container['session'];
    }

    /**
     * @return Validator
     */
    protected function getValidator()
    {
        return $this->container['validator'];
    }

    /**
     * @return FormFactory
     */
    protected function getFormFactory()
    {
        return $this->container['form.factory'];
    }

    /**
     * @return HttpCache
     */
    protected function getHttpCache()
    {
        return $this->container['http_cache'];
    }

    /**
     * @return SecurityContext
     */
    protected function getSecurity()
    {
        return $this->container['security'];
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getMailer()
    {
        return $this->container['mailer'];
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->container['monolog'];
    }

    /**
     * @return Translator
     */
    protected function getTranslator()
    {
        return $this->container['translator'];
    }

    /**
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->container['doctrine'];
    }
}