<?php

namespace App\Form\Field;

use App\Controller\Admin\ProductsCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ProductField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_ALLOW_ADD = 'allowAdd';
    public const OPTION_ALLOW_DELETE = 'allowDelete';
    public const OPTION_ENTRY_IS_COMPLEX = 'entryIsComplex';
    public const OPTION_ENTRY_TYPE = 'entryType';
    public const OPTION_SHOW_ENTRY_LABEL = 'showEntryLabel';
    public const OPTION_RENDER_EXPANDED = 'renderExpanded';
    public const OPTION_ENTRY_USES_CRUD_FORM = 'entryUsesCrudController';
    public const OPTION_ENTRY_CRUD_CONTROLLER_FQCN = 'entryCrudControllerFqcn';
    public const OPTION_ENTRY_CRUD_NEW_PAGE_NAME = 'entryCrudNewPageName';
    public const OPTION_ENTRY_CRUD_EDIT_PAGE_NAME = 'entryCrudEditPageName';

    /**
     * @param string $propertyName
     * @param TranslatableInterface|string|false|null $label
     * @return static
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/collection')
            ->setFormType(CollectionType::class)
            ->addCssClass('field-product')
            ->setColumns('col-12')
            ->setCustomOption(self::OPTION_ALLOW_ADD, true)
            ->setCustomOption(self::OPTION_ALLOW_DELETE, true)
            ->setCustomOption(self::OPTION_ENTRY_IS_COMPLEX, null)
            ->setCustomOption(self::OPTION_ENTRY_TYPE, null)
            ->setCustomOption(self::OPTION_SHOW_ENTRY_LABEL, false)
            ->setCustomOption(self::OPTION_RENDER_EXPANDED, false)
            ->setCustomOption(self::OPTION_ENTRY_USES_CRUD_FORM, true)
            ->setCustomOption(self::OPTION_ENTRY_CRUD_CONTROLLER_FQCN, ProductsCrudController::class)
            ->setCustomOption(self::OPTION_ENTRY_CRUD_NEW_PAGE_NAME, null)
            ->setCustomOption(self::OPTION_ENTRY_CRUD_EDIT_PAGE_NAME, null)
            // Add field collection js file
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-collection.js')
                ->onlyOnForms());
    }

    /**
     * @param bool $allow
     * @return $this
     */
    public function allowAdd(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_ADD, $allow);

        return $this;
    }

    /**
     * @param bool $allow
     * @return $this
     */
    public function allowDelete(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_DELETE, $allow);

        return $this;
    }

    /**
     * Set this option to TRUE if the collection items are complex form types
     * composed of several form fields (EasyAdmin applies a special rendering to make them look better).
     * @param bool $isComplex
     * @return $this
     */
    public function setEntryIsComplex(bool $isComplex = true): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_IS_COMPLEX, $isComplex);

        return $this;
    }

    /**
     * @param string $formTypeFqcn
     * @return $this
     */
    public function setEntryType(string $formTypeFqcn): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_TYPE, $formTypeFqcn);

        return $this;
    }

    /**
     * @param bool $showLabel
     * @return $this
     */
    public function showEntryLabel(bool $showLabel = true): self
    {
        $this->setCustomOption(self::OPTION_SHOW_ENTRY_LABEL, $showLabel);

        return $this;
    }

    /**
     * @param bool $renderExpanded
     * @return $this
     */
    public function renderExpanded(bool $renderExpanded = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_EXPANDED, $renderExpanded);

        return $this;
    }

    /**
     * @param string|null $crudControllerFqcn
     * @param string|null $crudNewPageName
     * @param string|null $crudEditPageName
     * @return $this
     */
    public function useEntryCrudForm(?string $crudControllerFqcn = null, ?string $crudNewPageName = null, ?string $crudEditPageName = null): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_USES_CRUD_FORM, true);
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_CONTROLLER_FQCN, $crudControllerFqcn);
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_NEW_PAGE_NAME, $crudNewPageName);
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_EDIT_PAGE_NAME, $crudEditPageName);

        return $this;
    }

    /**
     * @param string $crudPageName
     * @return $this
     */
    public function setEntryCrudPageName(string $crudPageName): self
    {
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_NEW_PAGE_NAME, $crudPageName);
        $this->setCustomOption(self::OPTION_ENTRY_CRUD_EDIT_PAGE_NAME, $crudPageName);

        return $this;
    }
}
