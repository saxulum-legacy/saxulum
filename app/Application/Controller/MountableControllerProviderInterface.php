<?php

namespace Application\Controller;

use Silex\ControllerProviderInterface;

interface MountableControllerProviderInterface extends ControllerProviderInterface
{
    /**
     * Return the mount string
     *
     * @return string
     */
    public function getMount();
}
