<?php

namespace App\Form\Field\Configurator;

use App\Form\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

final class PercentConfigurator implements FieldConfiguratorInterface
{
    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @return bool
     */
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return PercentField::class === $field->getFieldFqcn();
    }

    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @param AdminContext $context
     * @return void
     */
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $scale = $field->getCustomOption(PercentField::OPTION_NUM_DECIMALS);
        $roundingMode = $field->getCustomOption(PercentField::OPTION_ROUNDING_MODE);
        $symbol = $field->getCustomOption(PercentField::OPTION_SYMBOL);
        $isStoredAsFractional = true === $field->getCustomOption(PercentField::OPTION_STORED_AS_FRACTIONAL);

        $field->setFormTypeOptionIfNotSet('scale', $scale);
        $field->setFormTypeOptionIfNotSet('symbol', $symbol);
        $field->setFormTypeOptionIfNotSet('type', $isStoredAsFractional ? 'fractional' : 'integer');
        $field->setFormTypeOptionIfNotSet('rounding_mode', $roundingMode);

        if (null === $field->getValue()) {
            return;
        }

        $value = $field->getValue();
        $field->setFormattedValue(sprintf('%s%s', $isStoredAsFractional ? 100 * $value : $value, $symbol));
    }
}
