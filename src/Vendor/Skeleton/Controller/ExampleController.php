<?php

namespace Vendor\Skeleton\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vendor\Skeleton\Entity\Example;
use Vendor\Skeleton\Form\Type\ExampleType;

class ExampleController extends AbstractController
{
    public static function addRoutes(Application $app, $serviceId)
    {
        $app
            ->match('/', $serviceId . ':listAction')
            ->bind('example_list')
        ;
        $app
            ->match('/edit/{id}', $serviceId . ':editAction')
            ->value('id', null)
            ->assert('id', '\d+')
            ->bind('example_edit')
        ;
    }

    /**
     * @return string
     */
    public function listAction()
    {
        $examples = $this->getDoctrine()->getManager()->getRepository(get_class(new Example()))->findAll();

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
        if (is_null($id)) {
            $example = new Example();
        } else {
            $example = $this->getDoctrine()->getManager()->getRepository(get_class(new Example()))->find($id);
            if (is_null($example)) {
                throw new NotFoundHttpException("Can't find example with id {$id}");
            }
        }

        $exampleForm = $this->createForm(new ExampleType(), $example);

        if ('POST' === $request->getMethod()) {
            $exampleForm->bind($request);
            if ($exampleForm->isValid()) {
                $this->getDoctrine()->getManager()->persist($example);
                $this->getDoctrine()->getManager()->flush();

                return new RedirectResponse($this->getUrlGenerator()->generate('example_edit', array('id' => $example->getId())));
            }
        }

        return $this->renderView('@VendorSkeleton/Example/edit.html.twig', array(
            'example' => $example,
            'exampleForm' => $exampleForm->createView(),
        ));
    }
}
