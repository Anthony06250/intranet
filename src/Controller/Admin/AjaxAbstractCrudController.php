<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AjaxAbstractCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    abstract public function getAjaxTemplate(): string;

    /**
     * @param AdminContext $context
     * @return KeyValueStore|RedirectResponse|JsonResponse|Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function new(AdminContext $context): KeyValueStore|RedirectResponse|JsonResponse|Response
    {
        if (!$context->getRequest()->isXmlHttpRequest()) {
            return parent::new($context);
        }

        $context->getEntity()->setInstance($this->createEntity($context->getEntity()->getFqcn()));
        $this->container->get(EntityFactory::class)->processFields($context->getEntity(), FieldCollection::new($this->configureFields(Crud::PAGE_NEW)));

        $newForm = $this->createNewForm($context->getEntity(), $context->getCrud()->getNewFormOptions(), $context);

        $newForm->handleRequest($context->getRequest());

        if ($newForm->isSubmitted()) {
            $entityInstance = $newForm->getData();

            $this->persistEntity($this->container->get('doctrine')->getManagerForClass($context->getEntity()->getFqcn()), $entityInstance);

            return new JsonResponse($entityInstance->getId());
        }

        return new JsonResponse($this->renderView($this->getAjaxTemplate(), [
            'form' => $newForm,
        ]));
    }
}