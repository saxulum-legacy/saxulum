<?php

namespace Vendor\Skeleton;

use Saxulum\BundleProvider\Provider\AbstractBundleProvider;
use Silex\Application;

class SkeletonProvider extends AbstractBundleProvider
{
    public function register(Application $app)
    {
        $this->addCommands($app);
        $this->addControllers($app);
        $this->addDoctrineOrmMappings($app);
        $this->addTranslatorRessources($app);
        $this->addTwigLoaderFilesystemPath($app);
    }

    public function boot(Application $app) {}
}
