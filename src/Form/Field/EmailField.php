<?php

namespace App\Form\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class EmailField implements FieldInterface
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
            ->setTemplateName('crud/field/email')
            ->setFormType(EmailType::class)
            ->addCssClass('field-email')
            ->setColumns('col-6');
    }
}
