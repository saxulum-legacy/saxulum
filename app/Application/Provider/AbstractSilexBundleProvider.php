<?php

namespace Application\Provider;

use Application\Controller\AbstractController;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

abstract class AbstractSilexBundleProvider implements ServiceProviderInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    public function boot(Application $app) {}

    public function register(Application $app)
    {
        $this->app = $app;
        $this->addController();
        $this->addDoctrineOrmMappings();
        $this->addTranslatorRessources();
        $this->addTwigLoaderFilesystemPath();
    }

    protected function addController()
    {
        foreach(glob($this->getPath() . '/Controller/*Controller.php') as $controllerFilePath) {
            $controllerClassName = basename($controllerFilePath, '.php');
            $controllerNamespace = $this->getNamespace() . '\\Controller\\' . $controllerClassName;
            $reflectionClass = new \ReflectionClass($controllerNamespace);
            if(!$reflectionClass->isAbstract() || $reflectionClass->isInterface()) {
                $controller = new $controllerNamespace();
                /** @var AbstractController $controller */
                $this->app->mount($controller->getMount(), $controller);
            }
        }
    }

    protected function addDoctrineOrmMappings()
    {
        $ormEmOptions = $this->app['orm.em.options'];
        $ormEmOptions['mappings'][] = array(
            'type' => 'annotation',
            'namespace' => $this->getNamespace() . '\Entity',
            'path' => $this->getPath() .'/Entity',
            'use_simple_annotation_reader' => false,
        );
        $this->app['orm.em.options'] = $ormEmOptions;
    }

    protected function addTranslatorRessources()
    {
        $path = $this->getPath();
        $this->app['translator'] = $this->app->share($this->app->extend('translator',
            function(Translator $translator) use ($path) {
                $translator->addLoader('yaml', new YamlFileLoader());
                foreach(glob($path . '/Resources/translations/*.yml') as $yamlFilePath) {
                    $domainAndLocale = explode('.', basename($yamlFilePath, '.yml'));
                    if(count($domainAndLocale) == 2) {
                        $translator->addResource('yaml', $yamlFilePath, $domainAndLocale[1], $domainAndLocale[0]);
                    }
                }
                return $translator;
            }
        ));
    }

    protected function addTwigLoaderFilesystemPath()
    {
        $path = $this->getPath();
        $namespace = $this->getNamespace();
        $this->app['twig.loader.filesystem'] = $this->app->share($this->app->extend('twig.loader.filesystem',
            function(\Twig_Loader_Filesystem $twigLoaderFilesystem) use ($path, $namespace) {
                $twigLoaderFilesystem->addPath($path. '/Resources/views', str_replace('\\', '', $namespace));
                return $twigLoaderFilesystem;
            }
        ));
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        if(is_null($this->reflectionClass)) {
            $this->assignReflectionClass();
        }
        return $this->reflectionClass->getNamespaceName();
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        if(is_null($this->reflectionClass)) {
            $this->assignReflectionClass();
        }
        return dirname($this->reflectionClass->getFileName());
    }

    protected function assignReflectionClass()
    {
        $this->reflectionClass = new \ReflectionClass(get_class($this));
    }
}