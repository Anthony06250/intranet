<?php

namespace App\Controller\Admin;

use App\Entity\InvoicesTaxesRates;
use App\Form\Field\PercentField;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class InvoicesTaxesRatesCrudController extends AbstractCrudController
{
    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_ADMIN';
    public const ROLE_NEW = 'ROLE_ADMIN';
    public const ROLE_EDIT = 'ROLE_ADMIN';
    public const ROLE_DELETE = 'ROLE_SUPER_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return InvoicesTaxesRates::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('InvoicesTaxesRates.InvoicesTaxesRate')
            ->setEntityLabelInPlural('InvoicesTaxesRates.InvoicesTaxesRates')
            ->setPageTitle(Crud::PAGE_INDEX, 'InvoicesTaxesRates.List of taxes rates')
            ->setPageTitle(Crud::PAGE_NEW, 'InvoicesTaxesRates.Create taxes rate')
            ->setPageTitle(Crud::PAGE_EDIT, 'InvoicesTaxesRates.Edit taxes rate')
            ->setPageTitle(Crud::PAGE_DETAIL, 'InvoicesTaxesRates.View taxes rate')
            ->showEntityActionsInlined();
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('label', 'Forms.Labels.Label')
            ->addCssClass('fw-bold');
        yield PercentField::new('rate', 'Forms.Labels.Taxes rate');
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
    }}
