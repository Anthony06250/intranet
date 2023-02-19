<?php

namespace App\Controller\Admin;

use App\Entity\Invoices;
use App\Form\Field\AssociationField;
use App\Form\Field\CustomerField;
use App\Form\Field\DateField;
use App\Form\Field\MoneyField;
use App\Repository\StoresRepository;
use App\Repository\UsersRepository;
use App\Service\PdfService;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class InvoicesCrudController extends AbstractCrudController
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
    public const ROLE_NEW = 'ROLE_MANAGER';
    public const ROLE_EDIT = 'ROLE_MANAGER';
    public const ROLE_DELETE = 'ROLE_ADMIN';

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Invoices::class;
    }

    /**
     * @param SearchDto $searchDto
     * @param EntityDto $entityDto
     * @param FieldCollection $fields
     * @param FilterCollection $filters
     * @return QueryBuilder
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if (!$this->isGranted('ROLE_ADMIN')) {
            $queryBuilder->where('entity.store IN (:ids)')
                ->setParameter('ids', $this->getStoresForUser());
        }

        return $queryBuilder;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Invoices.Invoice')
            ->setEntityLabelInPlural('Invoices.Invoices')
            ->setPageTitle(Crud::PAGE_INDEX, 'Invoices.List of invoices')
            ->setPageTitle(Crud::PAGE_NEW, 'Invoices.Create invoice')
            ->setPageTitle(Crud::PAGE_EDIT, 'Invoices.Edit invoice')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Invoices.View invoice')
            ->setDefaultSort([
                'createdAt' => 'ASC'
            ])
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'crud/new' => 'bundles/EasyAdminBundle/crud/invoices.html.twig',
                'crud/edit' => 'bundles/EasyAdminBundle/crud/invoices.html.twig'
            ]);
    }

    /**
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile(Asset::new('assets/js/page/page.invoices.js')
            ->onlyOnForms());

        return $assets;
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
        yield $this->getUsersField();
        yield $this->getStoresField();

        yield DateField::new('selledAt', 'Forms.Labels.Selled at');
        yield DateField::new('createdAt', 'Forms.Labels.Created at')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ]);
        yield CustomerField::new('customer', 'Forms.Labels.Customer');
        yield CollectionField::new('products', 'Forms.Labels.Products')
            ->useEntryCrudForm(ProductsCrudController::class)
            ->setColumns('col-12');
        yield MoneyField::new('totalWithoutTaxes', 'Forms.Labels.Total without taxes')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => true
            ])
            ->setColumns('col-5');
        yield AssociationField::new('taxesRate', 'Forms.Labels.Taxes rate')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'choice_attr' => function ($choice) {
                    return [
                        'data-rate' => $choice->getRate()
                    ];
                }
            ])
            ->setColumns('col-2');
        yield MoneyField::new('totalWithTaxes', 'Forms.Labels.Total with taxes')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => true
            ])
            ->setColumns('col-5');
        yield MoneyField::new('taxesAmount', 'Forms.Labels.Taxes amount')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => true
            ]);
        yield AssociationField::new('paymentsMethod', 'Forms.Labels.Payment method')
            ->hideOnIndex()
            ->setFormTypeOption('placeholder', 'Forms.Placeholders.Payment method');
    }

    /**
     * @return AssociationField
     */
    private function getUsersField(): AssociationField
    {
        $usersField = AssociationField::new('user', 'Forms.Labels.User')
            ->addCssClass('fw-bold');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $usersField->setFormTypeOptions([
                'query_builder' => function (UsersRepository $usersRepository) {
                    return $usersRepository->createQueryBuilder('u')
                        ->where('u.id = (:id)')
                        ->setParameter('id', $this->getUser()->getId());
                }
            ]);
        }

        return $usersField;
    }

    /**
     * @return AssociationField
     */
    private function getStoresField(): AssociationField
    {
        $storesField = AssociationField::new('store', 'Forms.Labels.Store');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $storesField->setFormTypeOptions([
                'query_builder' => function (StoresRepository $storesRepository) {
                    return $storesRepository->createQueryBuilder('s')
                        ->where('s.id IN (:ids)')
                        ->setParameter('ids', $this->getStoresForUser());
                }
            ]);
        }

        return $storesField;
    }

    /**
     * @return array
     */
    private function getStoresForUser(): array
    {
        $stores = [];

        foreach ($this->getUser()->getStores() as $store) {
            $stores[] = $store->getId();
        }

        return $stores;
    }

    /**
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Generate actions
            ->add(Crud::PAGE_INDEX, Action::new('generateDocuments', 'Invoices.Generate documents')
                ->linkToCrudAction('generateInvoicesDocuments'))
            ->update(Crud::PAGE_INDEX, 'generateDocuments',
                fn (Action $action) => $action->addCssClass('btn btn-outline-secondary btn-sm py-0 px-1 me-1'))

            // Permissions
            ->setPermissions([
                Action::INDEX => self::ROLE_INDEX,
                Action::NEW => self::ROLE_NEW,
                Action::EDIT => self::ROLE_EDIT,
                Action::DELETE => self::ROLE_DELETE
            ]);
    }

    /**
     * @param AdminContext $context
     * @param PdfService $pdfService
     * @return void
     */
    public function generateInvoicesDocuments(AdminContext $context, PdfService $pdfService): void
    {
        $locale = $context->getRequest()->getLocale();
        $invoices = $context->getEntity()->getInstance();
        $html = $this->render('pdf/generate.html.twig', [
            'class' => 'invoices',
            'document' => 'document',
            'locale' => $locale,
            'invoice' => $invoices
        ]);

        $pdfService->generatePdfFile('invoices-document-' . $locale . '-' . $invoices->getId(), $html);
    }
}
