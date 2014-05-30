<?php

namespace Vendor\Skeleton\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    protected $menuFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(FactoryInterface $menuFactory, TranslatorInterface $translator)
    {
        $this->menuFactory = $menuFactory;
        $this->translator = $translator;
    }

    public function buildMenu(Request $request)
    {
        $menu = $this->menuFactory->createItem('root');

        $menu->addChild($this->translator->trans('nav.example_list'), array(
            'route' => 'example_list'
        ));

        return $menu;
    }
}