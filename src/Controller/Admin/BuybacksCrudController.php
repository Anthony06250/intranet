<?php

namespace App\Controller\Admin;

use App\DBAL\Types\BuybacksStatusesType;
use App\Entity\Buybacks;
use App\Form\Field\AssociationField;
use App\Form\Field\ChoiceField;
use App\Form\Field\DateField;
use App\Form\Field\IntegerField;
use App\Form\Field\MoneyField;
use App\Form\Field\PercentField;
use App\Form\Field\TextareaField;
use App\Form\Field\TextField;
use App\Repository\StoresRepository;
use App\Repository\UsersRepository;
use App\Service\PdfService;
use App\Workflow\BuybacksWorkflow;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class BuybacksCrudController extends AbstractCrudController
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
     * @param BuybacksWorkflow $buybacksStateMachine
     */
    public function __construct(private readonly BuybacksWorkflow $buybacksStateMachine)
    {
    }

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Buybacks::class;
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
            ->setEntityLabelInSingular('Buybacks.Buyback')
            ->setEntityLabelInPlural('Buybacks.Buybacks')
            ->setPageTitle(Crud::PAGE_INDEX, 'Buybacks.List of buybacks')
            ->setPageTitle(Crud::PAGE_NEW, 'Buybacks.Create buyback')
            ->setPageTitle(Crud::PAGE_EDIT, 'Buybacks.Edit buyback')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Buybacks.View buyback')
            ->setDefaultSort([
                'due_at' => 'DESC'
            ])
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'crud/new' => 'bundles/EasyAdminBundle/crud/buybacks.html.twig',
                'crud/edit' => 'bundles/EasyAdminBundle/crud/buybacks.html.twig'
            ]);
    }

    /**
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets
            ->addJsFile(Asset::new('assets/js/page/page.buybacks.js')
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

        yield TextField::new('product', 'Forms.Labels.Product')
            ->setColumns('col-12');
        yield TextField::new('serial_number', 'Forms.Labels.Serial number')
            ->hideOnIndex();
        yield AssociationField::new('customer', 'Forms.Labels.Customer')
            ->setFormTypeOption('placeholder', 'Forms.Placeholders.Customers')
            // TODO: Customer will be necessary
            ->setRequired(false)
            ->setColumns('col-12');
        yield ChoiceField::new('status', 'Forms.Labels.Status')
            ->setChoices(BuybacksStatusesType::getChoices())
            ->setRequired(true)
            ->setFormTypeOption('placeholder', false);
        yield MoneyField::new('starting_price', 'Forms.Labels.Starting price')
            ->hideOnIndex()
            ->setColumns('col-5');
        yield PercentField::new('increased_percent', 'Forms.Labels.Increased percent')
            ->hideOnIndex()
            ->setColumns('col-2');
        yield MoneyField::new('increased_price', 'Forms.Labels.Increased price')
            ->hideOnIndex()
            ->setColumns('col-5');
        yield DateField::new('created_at', 'Forms.Labels.Created at')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'readonly' => !$this->isGranted('ROLE_ADMIN')
            ])
            ->setColumns('col-5');
        yield IntegerField::new('duration', 'Forms.Labels.Duration')
            ->hideOnIndex()
            ->setFormTypeOption('attr', [
                'min' => 1
            ])
            ->setColumns('col-2');
        yield DateField::new('due_at', 'Forms.Labels.Due at')
            ->setFormTypeOption('attr', [
                'readonly' => true
            ])
            ->setColumns('col-5');
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
            ->add(Crud::PAGE_INDEX, Action::new('recover', 'Buybacks.Statuses.Recovered')
                ->linkToCrudAction('recover')
                ->displayIf(fn (Buybacks $buyback) => ($this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $buyback->getUser()->getId())
                    && $this->buybacksStateMachine->canRecover($buyback))
                ->setCssClass('btn btn-outline-success btn-sm py-0 px-1 me-1'))
            ->add(Crud::PAGE_INDEX, Action::new('expire', 'Buybacks.Statuses.Expired')
                ->linkToCrudAction('expire')
                ->displayIf(fn (Buybacks $buyback) => ($this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $buyback->getUser()->getId())
                    && $this->buybacksStateMachine->canExpire($buyback))
                ->setCssClass('btn btn-outline-primary btn-sm py-0 px-1 me-1'))

            // Generate actions
            ->add(Crud::PAGE_INDEX, Action::new('generateContracts', 'Buybacks.Generate contracts')
                ->linkToCrudAction('generateBuybacksContracts'))
            ->update(Crud::PAGE_INDEX, 'generateContracts',
                fn (Action $action) => $action->addCssClass('btn btn-outline-secondary btn-sm py-0 px-1 me-1')
                ->displayIf(fn (Buybacks $buyback) => ($this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $buyback->getUser()->getId())
                    && $this->buybacksStateMachine->canRecover($buyback)))
            ->add(Crud::PAGE_INDEX, Action::new('generateRepossessions', 'Buybacks.Generate repossessions')
                ->linkToCrudAction('generateBuybacksRepossessions'))
            ->update(Crud::PAGE_INDEX, 'generateRepossessions',
                fn (Action $action) => $action->addCssClass('btn btn-outline-secondary btn-sm py-0 px-1 me-1')
                ->displayIf(fn (Buybacks $buyback) => ($this->isGranted('ROLE_ADMIN')
                        || $this->getUser()->getId() === $buyback->getUser()->getId())
                    && !$this->buybacksStateMachine->isExpire($buyback)
                    && !$this->buybacksStateMachine->canRecover($buyback)))

            // Reorder
            ->reorder(Crud::PAGE_INDEX, [
                'generateContracts',
                'generateRepossessions',
                'recover',
                'expire',
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
    public function recover(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->changeWorkflowPlace(BuybacksWorkflow::TRANSITION_RECOVER, $context, $adminUrlGenerator);
    }

    /**
     * @param AdminContext $context
     * @param AdminUrlGenerator $adminUrlGenerator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function expire(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->changeWorkflowPlace(BuybacksWorkflow::TRANSITION_EXPIRE, $context, $adminUrlGenerator);
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
            $this->buybacksStateMachine->$place($buyback);
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
    public function generateBuybacksContracts(AdminContext $context, PdfService $pdfService): void
    {
        $locale = $context->getRequest()->getLocale();
        $buyback = $context->getEntity()->getInstance();
        $html = $this->render('pdf/generate.html.twig', [
            'class' => 'buybacks',
            'document' => 'contract',
            'locale' => $locale,
            'buyback' => $buyback
        ]);

        $pdfService->generatePdfFile('buyback-contract-' . $locale . '-' . $buyback->getId(), $html);
    }

    /**
     * @param AdminContext $context
     * @param PdfService $pdfService
     * @return void
     */
    public function generateBuybacksRepossessions(AdminContext $context, PdfService $pdfService): void
    {
        $locale = $context->getRequest()->getLocale();
        $buyback = $context->getEntity()->getInstance();
        $html = $this->render('pdf/generate.html.twig', [
            'class' => 'buybacks',
            'document' => 'repossession',
            'locale' => $locale,
            'buyback' => $buyback
        ]);

        $pdfService->generatePdfFile('buyback-repossession-' . $locale . '-' . $buyback->getId(), $html);
    }
}
