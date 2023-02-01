<?php

namespace App\Controller\Admin;

use App\Entity\ControlsPeriods;
use App\Form\Field\BooleanField;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ControlsPeriodsCrudController extends AbstractCrudController
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
        return ControlsPeriods::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('ControlsPeriods.ControlsPeriod')
            ->setEntityLabelInPlural('ControlsPeriods.ControlsPeriods')
            ->setPageTitle(Crud::PAGE_INDEX, 'ControlsPeriods.List of controlsPeriods')
            ->setPageTitle(Crud::PAGE_NEW, 'ControlsPeriods.Create controlsPeriod')
            ->setPageTitle(Crud::PAGE_EDIT, 'ControlsPeriods.Edit controlsPeriod')
            ->setPageTitle(Crud::PAGE_DETAIL, 'ControlsPeriods.View controlsPeriod')
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
        yield BooleanField::new('debit', 'Forms.Labels.Allow debit')
            ->setFormTypeOption('disabled', !$this->isGranted('ROLE_ADMIN'));
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
