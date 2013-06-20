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
        $controllerMapJson = $this->getCacheDir() . '/controller.map.json';
        if (!$this->app['debug'] && is_file($controllerMapJson)) {
            $controllerMap = json_decode(file_get_contents($controllerMapJson), true);
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

        $app = $this->app;
        foreach ($controllerMap as $serviceId => $controllerNamespace) {
            $this->app[$serviceId] = $this->app->share(function() use ($app, $controllerNamespace) {
                return new $controllerNamespace($app);
            });
            $controllerNamespace::addRoutes($this->app, $serviceId);
        }
    }

    protected function addDoctrineOrmMappings()
    {
        if (!isset($this->app['orm.em.options'])) {
            $this->app['orm.em.options'] = array(
                'mappings' => array(),
            );
        }

        $this->app['orm.em.options'] = array_merge_recursive($this->app['orm.em.options'], array(
            'mappings' => array(
                array(
                    'type' => 'annotation',
                    'namespace' => $this->getNamespace() . '\Entity',
                    'path' => $this->getPath() .'/Entity',
                    'use_simple_annotation_reader' => false,
                )
            ),
        ));
    }

    protected function addTranslatorRessources()
    {
        $translationMapJson = $this->getCacheDir() . '/translation.map.json';
        if (!$this->app['debug'] && is_file($translationMapJson)) {
            $translationMap = json_decode(file_get_contents($translationMapJson), true);
        } else {
            $translationMap = array();
            foreach (glob($this->getPath() . '/Resources/translations/*.yml') as $yamlFilePath) {
                $domainAndLocale = explode('.', basename($yamlFilePath, '.yml'));
                if (count($domainAndLocale) == 2) {
                    $translationMap[] = array(
                        'path' => $yamlFilePath,
                        'locale' => $domainAndLocale[1],
                        'domain' => $domainAndLocale[0]
                    );
                }
            }
            file_put_contents($translationMapJson, json_encode($translationMap));
        }

        $this->app['translator'] = $this->app->share($this->app->extend('translator',
            function(Translator $translator) use ($translationMap) {
                $translator->addLoader('yaml', new YamlFileLoader());
                foreach ($translationMap as $translation) {
                    $translator->addResource('yaml', $translation['path'], $translation['locale'], $translation['domain']);
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

    /**
     * @return string
     */
    protected function getCacheDir()
    {
        $cacheDir = $this->app['cache_dir'] . '/' . $this->namespaceToServiceId($this->getNamespace());
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        return $cacheDir;
    }

    protected function assignReflectionClass()
    {
        $this->reflectionClass = new \ReflectionClass(get_class($this));
    }
}
