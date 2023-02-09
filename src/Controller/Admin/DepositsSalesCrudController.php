<?php

namespace App\Controller\Admin;

use App\DBAL\Types\DepositsSalesStatusesType;
use App\Entity\DepositsSales;
use App\Form\Field\AssociationField;
use App\Form\Field\ChoiceField;
use App\Form\Field\DateField;
use App\Form\Field\MoneyField;
use App\Form\Field\TextareaField;
use App\Repository\StoresRepository;
use App\Repository\UsersRepository;
use App\Service\PdfService;
use App\Workflow\DepositsSalesWorkflow;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class DepositsSalesCrudController extends AbstractCrudController
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
    public const ROLE_NEW = 'ROLE_BUYER';
    public const ROLE_EDIT = 'ROLE_BUYER';
    public const ROLE_DELETE = 'ROLE_ADMIN';

    /**
     * @param DepositsSalesWorkflow $depositsSalesStateMachine
     */
    public function __construct(private readonly DepositsSalesWorkflow $depositsSalesStateMachine)
    {
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return DepositsSales::class;
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
            ->setEntityLabelInSingular('DepositsSales.DepositSales')
            ->setEntityLabelInPlural('DepositsSales.DepositsSales')
            ->setPageTitle(Crud::PAGE_INDEX, 'DepositsSales.List of deposits sales')
            ->setPageTitle(Crud::PAGE_NEW, 'DepositsSales.Create deposit sales')
            ->setPageTitle(Crud::PAGE_EDIT, 'DepositsSales.Edit deposit sales')
            ->setPageTitle(Crud::PAGE_DETAIL, 'DepositsSales.View deposit sales')
            ->setDefaultSort([
                'created_at' => 'ASC'
            ])
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'crud/new' => 'bundles/EasyAdminBundle/crud/deposits-sales.html.twig',
                'crud/edit' => 'bundles/EasyAdminBundle/crud/deposits-sales.html.twig'
            ]);
    }

    /**
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets->addJsFile(Asset::new('assets/js/page/page.deposits-sales.js')
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

        yield AssociationField::new('customer', 'Forms.Labels.Customer')
            ->renderAsEmbeddedForm(CustomersCrudController::class,
                'embedded_fields',
                'embedded_fields')
            ->setFormTypeOption('row_attr', [
                'accordion' => true,
                'expanded' => true
            ])
            ->setRequired(false)
            ->setColumns('col-12');
        yield CollectionField::new('products', 'Forms.Labels.Products')
            ->useEntryCrudForm(ProductsCrudController::class,
                'embedded_fields_without_barcode',
                'embedded_fields_without_barcode')
            ->setColumns('col-12');
        yield ChoiceField::new('status', 'Forms.Labels.Status')
            ->setChoices(DepositsSalesStatusesType::getChoices())
            ->setRequired(true)
            ->setFormTypeOption('placeholder', false);
        yield MoneyField::new('reserved_price', 'Forms.Labels.Reserved price')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => true
            ]);
        yield DateField::new('created_at', 'Forms.Labels.Created at')
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ]);
        yield TextareaField::new('comments', 'Forms.Labels.Comments')
            ->hideOnIndex()
            ->setColumns('col-12');
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
            // Workflow actions
            ->add(Crud::PAGE_INDEX, Action::new(DepositsSalesWorkflow::TRANSITION_SOLD, 'DepositsSales.Statuses.' . ucfirst(DepositsSalesStatusesType::STATE_SOLDED))
                ->linkToCrudAction(DepositsSalesWorkflow::TRANSITION_SOLD)
                ->displayIf(fn (DepositsSales $depositsSales) => $this->isGranted(self::ROLE_NEW)
                    && $this->depositsSalesStateMachine->canSold($depositsSales))
                ->setCssClass('btn btn-outline-success btn-sm py-0 px-1 me-1'))
            ->add(Crud::PAGE_INDEX, Action::new(DepositsSalesWorkflow::TRANSITION_PAID, 'DepositsSales.Statuses.' . ucfirst(DepositsSalesStatusesType::STATE_PAYED))
                ->linkToCrudAction(DepositsSalesWorkflow::TRANSITION_PAID)
                ->displayIf(fn (DepositsSales $depositsSales) => $this->isGranted(self::ROLE_NEW)
                    && $this->depositsSalesStateMachine->canPaid($depositsSales))
                ->setCssClass('btn btn-outline-primary btn-sm py-0 px-1 me-1'))
            ->add(Crud::PAGE_INDEX, Action::new(DepositsSalesWorkflow::TRANSITION_RECOVER, 'DepositsSales.Statuses.' . ucfirst(DepositsSalesStatusesType::STATE_RECOVERED))
                ->linkToCrudAction(DepositsSalesWorkflow::TRANSITION_RECOVER)
                ->displayIf(fn (DepositsSales $depositsSales) => $this->isGranted(self::ROLE_NEW)
                    && $this->depositsSalesStateMachine->canRecover($depositsSales))
                ->setCssClass('btn btn-outline-primary btn-sm py-0 px-1 me-1'))

            // Generate actions
            ->add(Crud::PAGE_INDEX, Action::new('generateContracts', 'DepositsSales.Generate contracts')
                ->linkToCrudAction('generateDepositsSalesContracts'))
            ->update(Crud::PAGE_INDEX, 'generateContracts',
                fn (Action $action) => $action->addCssClass('btn btn-outline-secondary btn-sm py-0 px-1 me-1')
                    ->displayIf(fn (DepositsSales $depositsSales) => $this->depositsSalesStateMachine->canSold($depositsSales)
                            || $this->depositsSalesStateMachine->canPaid($depositsSales)))

            // Reorder
            ->reorder(Crud::PAGE_INDEX, [
                'generateContracts',
                'sold',
                'paid',
                'recover',
                Action::DETAIL,
                Action::EDIT,
                Action::DELETE
            ])

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
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sold(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->changeWorkflowPlace(DepositsSalesWorkflow::TRANSITION_SOLD, $context, $adminUrlGenerator);
    }

    /**
     * @param AdminContext $context
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function paid(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->changeWorkflowPlace(DepositsSalesWorkflow::TRANSITION_PAID, $context, $adminUrlGenerator);
    }

    /**
     * @param AdminContext $context
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function recover(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->changeWorkflowPlace(DepositsSalesWorkflow::TRANSITION_RECOVER, $context, $adminUrlGenerator);
    }

    /**
     * @param string $place
     * @param AdminContext $context
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function changeWorkflowPlace(string $place, AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $buyback = $context->getEntity()->getInstance();

        $adminUrlGenerator->setController(self::class)->setAction('index')->removeReferrer()->setEntityId(null);

        try {
            $this->depositsSalesStateMachine->$place($buyback);
            $this->container->get('doctrine')->getManagerForClass($context->getEntity()->getFqcn())->flush();
        } catch (Exception) {
        }

        return $this->redirect($adminUrlGenerator->generateUrl());
    }

    /**
     * @param AdminContext $context
     * @param PdfService $pdfService
     * @return void
     */
    public function generateDepositsSalesContracts(AdminContext $context, PdfService $pdfService): void
    {
        $locale = $context->getRequest()->getLocale();
        $depositSales = $context->getEntity()->getInstance();
        $html = $this->render('pdf/generate.html.twig', [
            'class' => 'deposits-sales',
            'document' => 'contract',
            'locale' => $locale,
            'deposit_sales' => $depositSales,
            'copy_for' => true
        ]);

        $pdfService->generatePdfFile('deposit-sales-contract-' . $locale . '-' . $depositSales->getId(), $html);
    }
}
