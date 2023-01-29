<?php

namespace App\Form\Field;

use App\Form\Type\MonthType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class MonthField implements FieldInterface
{
    use FieldTrait;

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
            ->setTemplatePath('bundles/EasyAdminBundle/crud/field/month.html.twig')
            ->setFormType(MonthType::class)
            ->addCssClass('field-month')
            ->setColumns('col-6');
    }
}
