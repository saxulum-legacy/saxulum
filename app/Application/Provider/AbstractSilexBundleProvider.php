<?php

namespace Application\Provider;

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
        $app = $this->app;
        $controllerMapJson = $app['cache_dir'] . '/controllerMap.json';
        if(file_exists($controllerMapJson)) {
            $controllerMap = json_decode(file_get_contents($controllerMapJson));
        } else {
            $controllerMap = array();
            foreach (glob($this->getPath() . '/Controller/*Controller.php') as $controllerFilePath) {
                $controllerClassName = basename($controllerFilePath, '.php');
                $controllerNamespace = $this->getNamespace() . '\\Controller\\' . $controllerClassName;
                $reflectionClass = new \ReflectionClass($controllerNamespace);
                if($reflectionClass->implementsInterface('Application\Controller\ControllerRouteInterface') &&
                    !$reflectionClass->isAbstract() &&
                    !$reflectionClass->isInterface()) {
                    $controllerMap[$this->namespaceToServiceId($controllerNamespace)] = $controllerNamespace;
                }
            }
            file_put_contents($controllerMapJson, json_encode($controllerMap));
        }

        foreach($controllerMap as $serviceId => $controllerNamespace) {
            $app[$serviceId] = $app->share(function() use ($app, $controllerNamespace) {
                return new $controllerNamespace($app);
            });
            $controllerNamespace::addRoutes($app, $serviceId);
        }
    }

    protected function addDoctrineOrmMappings()
    {
        $emsOptions = isset($this->app['orm.em.options']) ? $this->app['orm.em.options'] : array();
        $emsOptions['mappings'][] = array(
            'type' => 'annotation',
            'namespace' => $this->getNamespace() . '\Entity',
            'path' => $this->getPath() .'/Entity',
            'use_simple_annotation_reader' => false,
        );
        $this->app['orm.em.options']= $emsOptions;
    }

    protected function addTranslatorRessources()
    {
        $path = $this->getPath();
        $this->app['translator'] = $this->app->share($this->app->extend('translator',
            function(Translator $translator) use ($path) {
                $translator->addLoader('yaml', new YamlFileLoader());
                foreach (glob($path . '/Resources/translations/*.yml') as $yamlFilePath) {
                    $domainAndLocale = explode('.', basename($yamlFilePath, '.yml'));
                    if (count($domainAndLocale) == 2) {
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
        if (is_null($this->reflectionClass)) {
            $this->assignReflectionClass();
        }

        return $this->reflectionClass->getNamespaceName();
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        if (is_null($this->reflectionClass)) {
            $this->assignReflectionClass();
        }

        return dirname($this->reflectionClass->getFileName());
    }

    /**
     * @param  string $namespace
     * @return string
     */
    protected function namespaceToServiceId($namespace)
    {
        return strtolower(str_replace("\\", '.', $namespace));
    }

    protected function assignReflectionClass()
    {
        $this->reflectionClass = new \ReflectionClass(get_class($this));
    }
}
