<?php

namespace Vendor\Skeleton;

use Saxulum\BundleProvider\Provider\AbstractBundleProvider;
use Silex\Application;
use Vendor\Skeleton\Provider\MenuProvider;

class SkeletonProvider extends AbstractBundleProvider
{
    public function register(Application $app)
    {
        $app->register(new MenuProvider());

        $this->addCommands($app);
        $this->addControllers($app);
        $this->addDoctrineOrmMappings($app);
        $this->addTranslatorRessources($app);
        $this->addTwigLoaderFilesystemPath($app);
    }

    public function boot(Application $app) {}
}
