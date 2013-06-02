<?php

namespace Vendor\Skeleton\Controller;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vendor\Skeleton\Entity\Example;
use Vendor\Skeleton\Form\Type\ExampleType;

class ExampleController extends AbstractController
{
    /**
     * @return string
     */
    public function getMount()
    {
        return '/';
    }

    /**
     * @param ControllerCollection $controllerCollection
     * @return ControllerCollection
     */
    protected function addRoutes(ControllerCollection $controllerCollection)
    {
        $controllerCollection->match('/', array($this, 'listAction'))->bind('example_list');
        $controllerCollection
            ->match('/edit/{id}', array($this, 'editAction'))
            ->value('id', null)
            ->assert('id', '\d+')
            ->bind('example_edit')
        ;
        return $controllerCollection;
    }

    /**
     * @return string
     */
    public function listAction()
    {
        $examples = $this->getEntityManager()->getRepository(get_class(new Example()))->findAll();

        return $this->renderView('@VendorSkeleton/Example/list.html.twig', array(
            'examples' => $examples,
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Request $request, $id)
    {
        if(is_null($id)) {
            $example = new Example();
        } else {
            $example = $this->getEntityManager()->getRepository(get_class(new Example()))->find($id);
            if(is_null($example)) {
                throw new NotFoundHttpException("Can't find example with id {$id}");
            }
        }

        $exampleForm = $this->createForm(new ExampleType(), $example);

        if('POST' === $request->getMethod()) {
            $exampleForm->bind($request);
            if($exampleForm->isValid()) {
                $this->getEntityManager()->persist($example);
                $this->getEntityManager()->flush();
                return new RedirectResponse($this->getUrlGenerator()->generate('example_edit', array('id' => $example->getId())));
            }
        }

        return $this->renderView('@VendorSkeleton/Example/edit.html.twig', array(
            'example' => $example,
            'exampleForm' => $exampleForm->createView(),
        ));
    }
}