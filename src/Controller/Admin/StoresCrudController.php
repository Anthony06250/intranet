<?php

namespace App\Controller\Admin;

use App\Entity\Stores;
use App\Form\Field\EmailField;
use App\Form\Field\TelephoneField;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StoresCrudController extends AbstractCrudController
{
    /**
     * Crud permissions
     */
    public const ROLE_INDEX = 'ROLE_SELLER';
    public const ROLE_NEW = 'ROLE_ADMIN';
    public const ROLE_EDIT = 'ROLE_ADMIN';
    public const ROLE_DELETE = 'ROLE_SUPER_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Stores::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Stores.Store')
            ->setEntityLabelInPlural('Stores.Stores')
            ->setPageTitle(Crud::PAGE_INDEX, 'Stores.List of stores')
            ->setPageTitle(Crud::PAGE_NEW, 'Stores.Create store')
            ->setPageTitle(Crud::PAGE_EDIT, 'Stores.Edit store')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Stores.View store')
            ->showEntityActionsInlined();
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('city', 'Forms.Labels.City')
            ->addCssClass('fw-bold');
        yield TextField::new('zipcode', 'Forms.Labels.Zipcode')
            ->hideOnIndex();
        yield TextField::new('address', 'Forms.Labels.Address');
        yield TextField::new('additional_address', 'Forms.Labels.AdditionalAddress')
            ->hideOnIndex();
        yield TelephoneField::new('phone', 'Forms.Labels.Phone');
        yield EmailField::new('email', 'Forms.Labels.Email');
        yield TextField::new('plus_code', 'Forms.Labels.PlusCode');
        yield TextField::new('commercialRegisterNumber', 'Forms.Labels.CRN')
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
