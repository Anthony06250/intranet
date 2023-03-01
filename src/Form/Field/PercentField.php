<?php

namespace App\Form\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use InvalidArgumentException;
use NumberFormatter;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class PercentField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_NUM_DECIMALS = 'numDecimals';
    public const OPTION_STORED_AS_FRACTIONAL = 'storedAsFractional';
    public const OPTION_SYMBOL = 'symbol';
    public const OPTION_ROUNDING_MODE = 'roundingMode';

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
            ->setTemplateName('crud/field/percent')
            ->setFormType(PercentType::class)
            ->addCssClass('field-percent')
            ->setTextAlign(TextAlign::RIGHT)
            ->setColumns('col-6')
            ->setCustomOption(self::OPTION_NUM_DECIMALS, 2)
            ->setCustomOption(self::OPTION_STORED_AS_FRACTIONAL, true)
            ->setCustomOption(self::OPTION_SYMBOL, '%')
            ->setCustomOption(self::OPTION_ROUNDING_MODE, NumberFormatter::ROUND_HALFUP)
            // Add percent field js file
            ->addWebpackEncoreEntries(Asset::new('field/percent')
                ->onlyOnForms());
    }

    /**
     * @param int $num
     * @return $this
     */
    public function setNumDecimals(int $num): self
    {
        if ($num < 0) {
            throw new InvalidArgumentException(sprintf('The argument of the "%s()" method must be 0 or higher (%d given).', __METHOD__, $num));
        }

        $this->setCustomOption(self::OPTION_NUM_DECIMALS, $num);

        return $this;
    }

    /**
     * @param bool $isFractional
     * @return $this
     */
    public function setStoredAsFractional(bool $isFractional = true): self
    {
        $this->setCustomOption(self::OPTION_STORED_AS_FRACTIONAL, $isFractional);

        return $this;
    }

    /**
     * @param bool|string $symbolOrFalse
     * @return $this
     */
    public function setSymbol(bool|string $symbolOrFalse): self
    {
        $this->setCustomOption(self::OPTION_SYMBOL, $symbolOrFalse);

        return $this;
    }

    /**
     * @param int $mode
     * @return $this
     */
    public function setRoundingMode(int $mode): self
    {
        $validModes = [
            'ROUND_DOWN' => NumberFormatter::ROUND_DOWN,
            'ROUND_FLOOR' => NumberFormatter::ROUND_FLOOR,
            'ROUND_UP' => NumberFormatter::ROUND_UP,
            'ROUND_CEILING' => NumberFormatter::ROUND_CEILING,
            'ROUND_HALF_DOWN' => NumberFormatter::ROUND_HALFDOWN,
            'ROUND_HALF_EVEN' => NumberFormatter::ROUND_HALFEVEN,
            'ROUND_HALF_UP' => NumberFormatter::ROUND_HALFUP,
        ];

        if (!in_array($mode, $validModes, true)) {
            throw new InvalidArgumentException(sprintf('The argument of the "%s()" method must be the value of any of the following constants from the %s class: %s.', __METHOD__, NumberFormatter::class, implode(', ', array_keys($validModes))));
        }

        $this->setCustomOption(self::OPTION_ROUNDING_MODE, $mode);

        return $this;
    }
}
