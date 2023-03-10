<?php

namespace App\Form\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class TimeField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_TIME_PATTERN = 'timePattern';
    public const OPTION_WIDGET = 'widget';

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
            ->setTemplateName('crud/field/time')
            ->setFormType(TimeType::class)
            ->addCssClass('field-time')
            ->setColumns('col-6')
            // the proper default values of these options are set on the Crud class
            ->setCustomOption(self::OPTION_TIME_PATTERN, null)
            ->setCustomOption(DateTimeField::OPTION_TIMEZONE, null)
            ->setCustomOption(self::OPTION_WIDGET, DateTimeField::WIDGET_NATIVE);
    }

    /**
     * @param string $timezoneId A valid PHP timezone ID
     * @return $this
     */
    public function setTimezone(string $timezoneId): self
    {
        if (!in_array($timezoneId, timezone_identifiers_list(), true)) {
            throw new InvalidArgumentException(sprintf('The "%s" timezone is not a valid PHP timezone ID. Use any of the values listed at https://www.php.net/manual/en/timezones.php', $timezoneId));
        }

        $this->setCustomOption(DateTimeField::OPTION_TIMEZONE, $timezoneId);

        return $this;
    }

    /**
     * @param string $timeFormatOrPattern A format name ('short', 'medium', 'long', 'full') or a valid ICU Datetime Pattern (see https://unicode-org.github.io/icu/userguide/format_parse/datetime/)
     * @return $this
     */
    public function setFormat(string $timeFormatOrPattern): self
    {
        if (DateTimeField::FORMAT_NONE === $timeFormatOrPattern || '' === trim($timeFormatOrPattern)) {
            $callable = static fn (string $format): bool => DateTimeField::FORMAT_NONE !== $format;
            $validTimeFormatsWithoutNone = array_filter(DateTimeField::VALID_DATE_FORMATS, $callable);

            throw new InvalidArgumentException(sprintf('The first argument of the "%s()" method cannot be "%s" or an empty string. Use either the special time formats (%s) or a datetime Intl pattern.', __METHOD__, DateTimeField::FORMAT_NONE, implode(', ', $validTimeFormatsWithoutNone)));
        }

        $this->setCustomOption(self::OPTION_TIME_PATTERN, $timeFormatOrPattern);

        return $this;
    }

    /**
     * @param bool $asNative
     * @return $this
     */
    public function renderAsNativeWidget(bool $asNative = true): self
    {
        if (false === $asNative) {
            $this->renderAsChoice();
        } else {
            $this->setCustomOption(self::OPTION_WIDGET, DateTimeField::WIDGET_NATIVE);
        }

        return $this;
    }

    /**
     * @param bool $asChoice
     * @return $this
     */
    public function renderAsChoice(bool $asChoice = true): self
    {
        if (false === $asChoice) {
            $this->renderAsNativeWidget();
        } else {
            $this->setCustomOption(self::OPTION_WIDGET, DateTimeField::WIDGET_CHOICE);
        }

        return $this;
    }

    /**
     * @param bool $asText
     * @return $this
     */
    public function renderAsText(bool $asText = true): self
    {
        if (false === $asText) {
            $this->renderAsNativeWidget();
        } else {
            $this->setCustomOption(self::OPTION_WIDGET, DateTimeField::WIDGET_TEXT);
        }

        return $this;
    }
}
