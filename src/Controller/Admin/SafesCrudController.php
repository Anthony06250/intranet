<?php

namespace App\Controller\Admin;

use App\Entity\Controls;
use App\Entity\ControlsCounters;
use App\Entity\ControlsPeriods;
use App\Entity\Safes;
use App\Entity\SafesControls;
use App\Entity\SafesMovements;
use App\Entity\SafesMovementsTypes;
use App\Form\Field\AssociationField;
use App\Form\Field\MonthField;
use App\Repository\StoresRepository;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Exception;
use IntlDateFormatter;
use Locale;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SafesCrudController extends AbstractCrudController
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
        return Safes::class;
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
            ->setEntityLabelInSingular('Safes.Safe')
            ->setEntityLabelInPlural('Safes.Safes')
            ->setPageTitle(Crud::PAGE_INDEX, 'Safes.List of safes')
            ->setPageTitle(Crud::PAGE_NEW, 'Safes.Create safe')
            ->setPageTitle(Crud::PAGE_EDIT, 'Safes.Edit safe')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Safes.View safe')
            ->setDefaultSort([
                'month' => 'DESC'
            ])
            ->setPaginatorPageSize(self::MAX_RESULTS_REQUEST)
            ->showEntityActionsInlined()
            ->overrideTemplates([
                'crud/new' => 'bundles/EasyAdminBundle/crud/safes.html.twig',
                'crud/edit' => 'bundles/EasyAdminBundle/crud/safes.html.twig',
                'crud/detail' => 'bundles/EasyAdminBundle/crud/safes.html.twig'
            ]);
    }

    /**
     * @param Assets $assets
     * @return Assets
     */
    public function configureAssets(Assets $assets): Assets
    {
        $assets->addWebpackEncoreEntry(Asset::new('page/safes')
            ->onlyOnDetail());

        return $assets;
    }

    /**
     * @param KeyValueStore $responseParameters
     * @return KeyValueStore
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            $responseParameters->set('pageLimit', self::RESULTS_PER_PAGE);
        }

        if (Crud::PAGE_DETAIL === $responseParameters->get('pageName')) {
            $safe = $responseParameters->get('entity')->getInstance();
            $month = explode('-', $safe->getMonth())[1];
            $year = explode('-', $safe->getMonth())[0];
            $dateFormatter = IntlDateFormatter::create(
                Locale::getDefault(),
                IntlDateFormatter::SHORT,
                IntlDateFormatter::NONE,
                null,
                IntlDateFormatter::GREGORIAN,
            );
            $controlsBuffer = [];

            for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, intval($month), intval($year)); $day++) {
                $date = $dateFormatter->format((new DateTimeImmutable($year . '-' . $month . '-' . $day))->getTimestamp());
                $controlsBuffer[$date] = [];
            }

            $responseParameters->set('controls', $this->getControlsForDetail($safe, $dateFormatter, $controlsBuffer));
            $responseParameters->set('safesMovements', $this->getSafesMovementsForDetail($safe, $dateFormatter));
            $responseParameters->set('safesControls', $this->getSafesControlsForDetail($safe, $dateFormatter));
        }

        return $responseParameters;
    }

    /**
     * @param Safes $safe
     * @param IntlDateFormatter $dateFormatter
     * @param array $controlsBuffer
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function getControlsForDetail(Safes $safe, IntlDateFormatter $dateFormatter, array $controlsBuffer): array
    {
        $controlsRepository = $this->container->get('doctrine')->getRepository(Controls::class);
        $controls = $controlsRepository->findControlsForSafe(new DateTimeImmutable($safe->getMonth()), $safe->getStore(), $safe->getPeriod());

        foreach ($controls as $control) {
            $controlsBuffer[$dateFormatter->format($control->getCreatedAt()->getTimestamp())]
            [$control->getCounter()->getId()][] = $control;
        }

        return $controlsBuffer;
    }

    /**
     * @param Safes $safe
     * @param IntlDateFormatter $dateFormatter
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function getSafesMovementsForDetail(Safes $safe, IntlDateFormatter $dateFormatter): array
    {
        $safesMovementsRepository = $this->container->get('doctrine')->getRepository(SafesMovements::class);
        $safesMovements = $safesMovementsRepository->findSafesMovementsForSafe(new DateTimeImmutable($safe->getMonth()), $safe->getStore());
        $safesMovementsBuffer = [];

        foreach ($safesMovements as $safesMovement) {
            $safesMovementsBuffer[$dateFormatter->format($safesMovement->getCreatedAt()->getTimestamp())]
            [$safesMovement->getMovementsType()->getLabel()][] = $safesMovement;
        }

        ksort($safesMovementsBuffer);

        return $safesMovementsBuffer;
    }

    /**
     * @param Safes $safe
     * @param IntlDateFormatter $dateFormatter
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function getSafesControlsForDetail(Safes $safe, IntlDateFormatter $dateFormatter): array
    {
        $safesControlsRepository = $this->container->get('doctrine')->getRepository(SafesControls::class);
        $safesControls = $safesControlsRepository->findSafesControlsForSafe(new DateTimeImmutable($safe->getMonth()), $safe->getStore());
        $safesControlsBuffer = [];

        foreach ($safesControls as $safesControl) {
            $safesControlsBuffer[$dateFormatter->format($safesControl->getCreatedAt()->getTimestamp())][] = $safesControl;
        }

        ksort($safesControlsBuffer);

        return $safesControlsBuffer;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        yield MonthField::new('month', 'Forms.Labels.Month')
            ->addCssClass('fw-bold');

        yield $this->getStoresField();

        yield AssociationField::new('counters', 'Forms.Labels.Counters');
        yield AssociationField::new('period', 'Forms.Labels.Period');
        yield AssociationField::new('movementsTypes', 'Forms.Labels.Movements types');
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
        // Permissions
        return $actions->setPermissions([
            Action::INDEX => self::ROLE_INDEX,
            Action::NEW => self::ROLE_NEW,
            Action::EDIT => self::ROLE_EDIT,
            Action::DELETE => self::ROLE_DELETE
        ]);
    }

    /**
     * @param string $entityFqcn
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createEntity(string $entityFqcn): mixed
    {
        $safe = parent::createEntity($entityFqcn);
        $entityManager = $this->container->get('doctrine')->getManager();
        $period = $entityManager->getRepository(ControlsPeriods::class)->find(2);
        $counters = $entityManager->getRepository(ControlsCounters::class)->findAll();
        $movementsTypes = $entityManager->getRepository(SafesMovementsTypes::class)->findAll();

        $safe->setPeriod($period);

        foreach ($counters as $counter) {
            $safe->addCounter($counter);
        }

        foreach ($movementsTypes as $movementsType) {
            $safe->addMovementsType($movementsType);
        }

        return $safe;
    }
}
