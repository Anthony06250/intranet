<?php

namespace App\Form\Field\Configurator;

use App\Form\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

final class EmailConfigurator implements FieldConfiguratorInterface
{
    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @return bool
     */
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return EmailField::class === $field->getFieldFqcn();
    }

    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @param AdminContext $context
     * @return void
     */
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $field->setFormTypeOptionIfNotSet('attr.inputmode', 'email');
    }
}
