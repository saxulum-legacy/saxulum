<?php

namespace Vendor\Skeleton\Controller;

use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Paginator;
use Silex\Application;
use Saxulum\DoctrineOrmManagerRegistry\Doctrine\ManagerRegistry;
use Saxulum\RouteController\Annotation\DI;
use Saxulum\RouteController\Annotation\Route;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Vendor\Skeleton\Entity\Example;
use Vendor\Skeleton\Form\Type\ExampleType;

/**
 * @DI(serviceIds={"doctrine", "twig", "form.factory", "knp_paginator", "url_generator"})
 */
class ExampleController
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param ManagerRegistry $doctrine
     * @param \Twig_Environment $twig
     * @param FormFactory $formFactory
     * @param Paginator $paginator
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(
        ManagerRegistry $doctrine,
        \Twig_Environment $twig,
        FormFactory $formFactory,
        Paginator $paginator,
        UrlGenerator $urlGenerator
    ) {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
        $this->urlGenerator = $urlGenerator;
    }
    
    /**
     * @Route("/", bind="example_list")
     * @return string
     */
    public function listAction(Request $request)
    {
        /** @var EntityRepository $repo */
        $repo = $this->doctrine->getManager()->getRepository(get_class(new Example()));

        $qb = $repo->createQueryBuilder('e');

        $examples = $this->paginator->paginate($qb, $request->query->get('page', 1), 10);

        return $this->twig->render('@VendorSkeleton/Example/list.html.twig', array(
            'examples' => $examples,
        ));
    }

    /**
     * @Route("/edit/{id}", bind="example_edit", asserts={"name"="\d+"}, values={"id"=null})
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
            $example = $this->doctrine->getManager()->getRepository(get_class(new Example()))->find($id);
            if (is_null($example)) {
                throw new NotFoundHttpException("Can't find example with id {$id}");
            }
        }

        $exampleForm = $this->formFactory->create(new ExampleType(), $example);

        if ('POST' === $request->getMethod()) {
            $exampleForm->submit($request);
            if ($exampleForm->isValid()) {
                $this->doctrine->getManager()->persist($example);
                $this->doctrine->getManager()->flush();

                return new RedirectResponse($this->urlGenerator->generate('example_edit', array('id' => $example->getId())));
            }
        }

        return $this->twig->render('@VendorSkeleton/Example/edit.html.twig', array(
            'example' => $example,
            'exampleForm' => $exampleForm->createView(),
        ));
    }

    /**
     * @Route("/delete/{id}", bind="example_delete", asserts={"name"="\d+"})
     * @param $id
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $example = $this->doctrine->getManager()->getRepository(get_class(new Example()))->find($id);

        if (is_null($example)) {
            throw new NotFoundHttpException("Can't find example with id {$id}");
        }

        $this->doctrine->getManager()->remove($example);
        $this->doctrine->getManager()->flush();

        return new RedirectResponse($this->urlGenerator->generate('example_list'), 302);
    }
}
