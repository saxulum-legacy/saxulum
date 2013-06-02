<?php

namespace Vendor\Skeleton\Controller;

use Application\Controller\AbstractController as BaseController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;

abstract class AbstractController extends BaseController
{
    /**
     * @param string $type
     * @param null $data
     * @param array $options
     * @param FormBuilderInterface $parent
     * @return Form
     */
    protected function createForm($type = 'form', $data = null, array $options = array(), FormBuilderInterface $parent = null)
    {
        return $this->getFormFactory()->createBuilder($type, $data, $options, $parent)->getForm();
    }

    /**
     * @param $view
     * @param array $parameters
     * @return string
     */
    protected function renderView($view, array $parameters = array())
    {
        return $this->getTwig()->render($view, $parameters);
    }
}