<?php

namespace Vendor\Skeleton\Controller;

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
 * @DI(serviceIds={"doctrine", "twig", "form.factory", "url_generator"})
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
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param ManagerRegistry $doctrine
     * @param \Twig_Environment $twig
     * @param FormFactory $formFactory
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(
        ManagerRegistry $doctrine,
        \Twig_Environment $twig,
        FormFactory $formFactory,
        UrlGenerator $urlGenerator
    ) {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }
    
    /**
     * @Route("/", bind="example_list")
     * @return string
     */
    public function listAction()
    {
        $examples = $this->doctrine->getManager()->getRepository(get_class(new Example()))->findAll();

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
            $exampleForm->handleRequest($request);
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
}
