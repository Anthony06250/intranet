<?php

namespace App\Form\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use InvalidArgumentException;
use NumberFormatter;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class NumberField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_NUM_DECIMALS = 'numDecimals';
    public const OPTION_ROUNDING_MODE = 'roundingMode';
    public const OPTION_STORED_AS_STRING = 'storedAsString';
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
            ->setTemplateName('crud/field/number')
            ->setFormType(NumberType::class)
            ->addCssClass('field-number')
            ->setColumns('col-6')
            ->setCustomOption(self::OPTION_NUM_DECIMALS, null)
            ->setCustomOption(self::OPTION_ROUNDING_MODE, NumberFormatter::ROUND_HALFUP)
            ->setCustomOption(self::OPTION_STORED_AS_STRING, false)
            ->setCustomOption(self::OPTION_NUMBER_FORMAT, null);
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

    /**
     * @param bool $asString
     * @return $this
     */
    public function setStoredAsString(bool $asString = true): self
    {
        $this->setCustomOption(self::OPTION_STORED_AS_STRING, $asString);

        return $this;
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
