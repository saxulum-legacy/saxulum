<?php

namespace Application\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\HttpCache;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validator;

abstract class AbstractController implements ControllerProviderInterface
{
    /**
     * @var Application
     */
    protected $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        return $this->addRoutes($this->getControllerFactory());
    }

    /**
     * @return string
     */
    abstract public function getMount();

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
        return $this->app['controllers_factory'];
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->app['twig'];
    }

    /**
     * @return UrlGenerator
     */
    protected function getUrlGenerator()
    {
        return $this->app['url_generator'];
    }

    /**
     * @return Session
     */
    protected function getSession()
    {
        return $this->app['session'];
    }

    /**
     * @return Validator
     */
    protected function getValidator()
    {
        return $this->app['validator'];
    }

    /**
     * @return FormFactory
     */
    protected function getFormFactory()
    {
        return $this->app['form.factory'];
    }

    /**
     * @return HttpCache
     */
    protected function getHttpCache()
    {
        return $this->app['http_cache'];
    }

    /**
     * @return SecurityContext
     */
    protected function setSecurity()
    {
        return $this->app['security'];
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getMailer()
    {
        return $this->app['mailer'];
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->app['monolog'];
    }

    /**
     * @return Translator
     */
    protected function getTranslator()
    {
        return $this->app['translator'];
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->app['db'];
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->app['orm.em'];
    }
}