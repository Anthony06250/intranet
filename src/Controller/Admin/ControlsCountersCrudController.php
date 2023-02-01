<?php

namespace App\Controller\Admin;

use App\Entity\ControlsCounters;
use App\Form\Field\BooleanField;
use App\Form\Field\MoneyField;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ControlsCountersCrudController extends AbstractCrudController
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
        return ControlsCounters::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('ControlsCounters.ControlsCounter')
            ->setEntityLabelInPlural('ControlsCounters.ControlsCounters')
            ->setPageTitle(Crud::PAGE_INDEX, 'ControlsCounters.List of controlsCounters')
            ->setPageTitle(Crud::PAGE_NEW, 'ControlsCounters.Create controlsCounter')
            ->setPageTitle(Crud::PAGE_EDIT, 'ControlsCounters.Edit controlsCounter')
            ->setPageTitle(Crud::PAGE_DETAIL, 'ControlsCounters.View controlsCounter')
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
        yield MoneyField::new('cashFund', 'Forms.Labels.Cash fund');
        yield BooleanField::new('reverse', 'Forms.Labels.Reverse calc')
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
