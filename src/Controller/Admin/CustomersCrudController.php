<?php

namespace App\Controller\Admin;

use App\Entity\Customers;
use App\Form\Field\AssociationField;
use App\Form\Field\DateField;
use App\Form\Field\EmailField;
use App\Form\Field\TelephoneField;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;

class CustomersCrudController extends AjaxAbstractCrudController
{
    /**
     * Index page settings
     */
    public const MAX_RESULTS_REQUEST = 320;
    public const RESULTS_PER_PAGE = 20;

    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_SELLER';
    public const ROLE_NEW = 'ROLE_SELLER';
    public const ROLE_EDIT = 'ROLE_JEWELER';
    public const ROLE_DELETE = 'ROLE_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Customers::class;
    }

    /**
     * @return string
     */
    public function getAjaxTemplate(): string
    {
        return 'bundles/EasyAdminBundle/crud/_ajax/_customers.html.twig';
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Customers.Customer')
            ->setEntityLabelInPlural('Customers.Customers')
            ->setPageTitle(Crud::PAGE_INDEX, 'Customers.List of customers')
            ->setPageTitle(Crud::PAGE_NEW, 'Customers.Create customer')
            ->setPageTitle(Crud::PAGE_EDIT, 'Customers.Edit customer')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Customers.View customer')
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'crud/new' => 'bundles/EasyAdminBundle/crud/customers.html.twig',
                'crud/edit' => 'bundles/EasyAdminBundle/crud/customers.html.twig'
            ]);
    }

    /**
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            $responseParameters->set('pageLimit', self::RESULTS_PER_PAGE);
        }

        return $responseParameters;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('civility', 'Forms.Labels.Civility')
            ->hideOnIndex()
            ->setFormTypeOption('placeholder', 'Forms.Placeholders.Civility')
            ->setColumns('col-2');
        yield TextField::new('firstname', 'Forms.Labels.Firstname')
            ->addCssClass('fw-bold')
            ->hideOnIndex()
            ->setColumns('col-5');
        yield TextField::new('lastname', 'Forms.Labels.Lastname')
            ->hideOnIndex()
            ->setColumns('col-5 mt-0');
        yield TextField::new('fullname', 'Forms.Labels.Fullname')
            ->addCssClass('fw-bold')
            ->onlyOnIndex();
        yield TextField::new('address', 'Forms.Labels.Address');
        yield TextField::new('additionalAddress', 'Forms.Labels.AdditionalAddress')
            ->hideOnIndex();
        yield TextField::new('zipcode', 'Forms.Labels.Zipcode');
        yield TextField::new('city', 'Forms.Labels.City');
        yield DateField::new('birthdayDate', 'Forms.Labels.Birthday')
            ->hideOnIndex();
        yield AssociationField::new('typesId', 'Forms.Labels.Types ID')
            ->hideOnIndex()
            ->setFormTypeOption('placeholder', 'Forms.Placeholders.Type id');
        yield TextField::new('idNumber', 'Forms.Labels.ID number')
            ->hideOnIndex();
        yield TelephoneField::new('phone', 'Forms.Labels.Phone');
        yield EmailField::new('email', 'Forms.Labels.Email')
            ->hideOnIndex();
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // Permissions
        return $actions->setPermissions([
            Action::INDEX => self::ROLE_INDEX,
            Action::NEW => self::ROLE_NEW,
            Action::EDIT => self::ROLE_EDIT,
            Action::DELETE => self::ROLE_DELETE
        ]);
    }
}
