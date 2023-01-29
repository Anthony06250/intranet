<?php

namespace App\Form\Field;

use App\Form\Type\TelephoneType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class TelephoneField implements FieldInterface
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
            ->setTemplateName('crud/field/telephone')
            ->setFormType(TelephoneType::class)
            ->addCssClass('field-telephone')
            ->setColumns('col-6');
    }
}
