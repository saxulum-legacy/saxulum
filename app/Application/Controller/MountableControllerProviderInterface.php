<?php

namespace Application\Controller;

use Silex\ControllerProviderInterface;

interface MountableControllerProviderInterface extends ControllerProviderInterface
{
    /**
     * Return the prefix string
     *
     * @return string
     */
    public function getPrefix();
}
