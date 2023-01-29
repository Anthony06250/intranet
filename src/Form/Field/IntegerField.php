<?php

namespace App\Form\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class IntegerField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_NUMBER_FORMAT = 'numberFormat';

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
            ->setTemplateName('crud/field/integer')
            ->setFormType(IntegerType::class)
            ->addCssClass('field-integer')
            ->setColumns('col-6')
            ->setCustomOption(self::OPTION_NUMBER_FORMAT, null)
            // Add integer field js file
            ->addJsFiles(Asset::new('assets/js/field/field.integer.js')
                ->onlyOnForms());
    }

    /**
     * @param string $sprintfFormat
     * @return $this
     */
    public function setNumberFormat(string $sprintfFormat): self
    {
        $this->setCustomOption(self::OPTION_NUMBER_FORMAT, $sprintfFormat);

        return $this;
    }
}
