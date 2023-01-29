<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\Field\AssociationField;
use App\Form\Field\BooleanField;
use App\Form\Field\DateField;
use App\Form\Field\EmailField;
use App\Form\Field\TelephoneField;
use App\Form\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UsersCrudController extends AbstractCrudController
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
    public const ROLE_NEW = 'ROLE_ADMIN';
    public const ROLE_EDIT = 'ROLE_SELLER';
    public const ROLE_DELETE = 'ROLE_SUPER_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Users::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Users.User')
            ->setEntityLabelInPlural('Users.Users')
            ->setPageTitle(Crud::PAGE_INDEX, 'Users.List of users')
            ->setPageTitle(Crud::PAGE_NEW, 'Users.Create user')
            ->setPageTitle(Crud::PAGE_EDIT, 'Users.Edit user')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Users.View user')
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined();
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
        yield TextField::new('username', 'Forms.Labels.Username')
            ->addCssClass('fw-bold')
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ]);
        yield TextField::new('plainPassword', 'Forms.Labels.Password')
            ->onlyOnForms()
            ->setRequired(($pageName === Action::NEW ?? false));
        yield AssociationField::new('civility', 'Forms.Labels.Civility')
            ->hideOnIndex()
            ->setColumns('col-2');
        yield TextField::new('firstname', 'Forms.Labels.Firstname')
            ->hideOnIndex()
            ->setColumns('col-5');
        yield TextField::new('lastname', 'Forms.Labels.Lastname')
            ->hideOnIndex()
            ->setColumns('col-5');
        yield TextField::new('fullname', 'Forms.Labels.Fullname')
            ->onlyOnIndex();
        yield AssociationField::new('usersPermission', 'Forms.Labels.Role')
            ->setFormTypeOption('disabled', !$this->isGranted('ROLE_ADMIN'));
        yield AssociationField::new('stores', 'Menu.Stores')
            ->setFormTypeOption('disabled', !$this->isGranted('ROLE_ADMIN'));
        yield DateField::new('birthdayDate', 'Forms.Labels.Birthday')
            ->hideOnIndex();
        yield DateField::new('hiringDate', 'Forms.Labels.Hiring date')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ]);
        yield TelephoneField::new('phone', 'Forms.Labels.Phone');
        yield EmailField::new('email', 'Forms.Labels.Email')
            ->hideOnIndex();
        yield BooleanField::new('active', 'Forms.Labels.Active')
            ->setFormTypeOption('disabled', !$this->isGranted('ROLE_ADMIN'));
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Index page
            ->update(Crud::PAGE_INDEX, Action::NEW,
                fn (Action $action) => $action->setCssClass('action-new btn btn-success'))
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL,
                fn (Action $action) => $action->addCssClass('btn btn-outline-info btn-sm py-0 px-1 me-1'))
            ->update(Crud::PAGE_INDEX, Action::EDIT,
                fn (Action $action) => $action->addCssClass('btn btn-outline-warning btn-sm py-0 px-1 me-1')
                    ->displayIf(fn ($entity) => $this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $entity->getId()))
            ->update(Crud::PAGE_INDEX, Action::DELETE,
                fn (Action $action) => $action->setCssClass('action-delete btn btn-outline-danger btn-sm py-0 px-1'))

            // New page
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->update(Crud::PAGE_NEW, Action::INDEX,
                fn (Action $action) => $action->setCssClass('action-index btn btn-secondary'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER,
                fn (Action $action) => $action->setCssClass('action-saveAndAddAnother btn btn-info action-save'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setCssClass('action-saveAndReturn btn btn-success action-save'))

            // Edit page
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->update(Crud::PAGE_EDIT, Action::INDEX,
                fn (Action $action) => $action->setCssClass('action-index btn btn-secondary'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE,
                fn (Action $action) => $action->setCssClass('action-saveAndContinue btn btn-info action-save'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setCssClass('action-saveAndReturn btn btn-warning action-save'))

            // Detail page
            ->update(Crud::PAGE_DETAIL, Action::DELETE,
                fn (Action $action) => $action->setCssClass('action-delete btn btn-danger text-white'))
            ->update(Crud::PAGE_DETAIL, Action::INDEX,
                fn (Action $action) => $action->setCssClass('action-index btn btn-secondary'))
            ->update(Crud::PAGE_DETAIL, Action::EDIT,
                fn (Action $action) => $action->setCssClass('action-edit btn btn-warning')
                    ->displayIf(fn ($entity) => $this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $entity->getId()))

            // Permissions
            ->setPermissions([
                Action::INDEX => self::ROLE_INDEX,
                Action::NEW => self::ROLE_NEW,
                Action::EDIT => self::ROLE_EDIT,
                Action::DELETE => self::ROLE_DELETE
            ]);
    }
}
