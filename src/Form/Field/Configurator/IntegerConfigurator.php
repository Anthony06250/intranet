<?php

namespace App\Form\Field\Configurator;

use App\Form\Field\IntegerField;
use App\Form\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

final class IntegerConfigurator implements FieldConfiguratorInterface
{
    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @return bool
     */
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return IntegerField::class === $field->getFieldFqcn();
    }

    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @param AdminContext $context
     * @return void
     */
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        if (null === $value = $field->getValue()) {
            return;
        }

        if (null !== $numberFormat = $field->getCustomOption(NumberField::OPTION_NUMBER_FORMAT)) {
            $field->setFormattedValue(sprintf($numberFormat, $value));
        } elseif (null !== $numberFormat = $context->getCrud()->getNumberFormat()) {
            $field->setFormattedValue(sprintf($numberFormat, $value));
        }
    }
}
