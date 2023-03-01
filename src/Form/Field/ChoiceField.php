<?php

namespace App\Form\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ChoiceField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_ALLOW_MULTIPLE_CHOICES = 'allowMultipleChoices';
    public const OPTION_AUTOCOMPLETE = 'autocomplete';
    public const OPTION_CHOICES = 'choices';
    public const OPTION_USE_TRANSLATABLE_CHOICES = 'useTranslatableChoices';
    public const OPTION_RENDER_AS_BADGES = 'renderAsBadges';
    public const OPTION_RENDER_EXPANDED = 'renderExpanded';
    public const OPTION_WIDGET = 'widget';
    public const OPTION_ESCAPE_HTML_CONTENTS = 'escapeHtml';
    public const VALID_BADGE_TYPES = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];
    public const WIDGET_AUTOCOMPLETE = 'autocomplete';
    public const WIDGET_NATIVE = 'native';

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
            ->setTemplateName('crud/field/choice')
            ->setFormType(ChoiceType::class)
            ->addCssClass('field-select')
            ->setColumns('col-6')
            ->setCustomOption(self::OPTION_CHOICES, null)
            ->setCustomOption(self::OPTION_USE_TRANSLATABLE_CHOICES, false)
            ->setCustomOption(self::OPTION_ALLOW_MULTIPLE_CHOICES, false)
            ->setCustomOption(self::OPTION_RENDER_AS_BADGES, null)
            ->setCustomOption(self::OPTION_RENDER_EXPANDED, false)
            ->setCustomOption(self::OPTION_WIDGET, null)
            ->setCustomOption(self::OPTION_ESCAPE_HTML_CONTENTS, true)
            // Add select2 attributes
            ->setFormTypeOption('attr', [
                'class' => 'select2',
                'data-toggle' => 'select2'
            ])
            // Add select2 js file
            ->addWebpackEncoreEntries(Asset::new('plugin/select2')
                ->onlyOnForms());
    }

    /**
     * @param bool $allow
     * @return $this
     */
    public function allowMultipleChoices(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_MULTIPLE_CHOICES, $allow);

        return $this;
    }

    /**
     * @return $this
     */
    public function autocomplete(): self
    {
        $this->setCustomOption(self::OPTION_AUTOCOMPLETE, true);

        return $this;
    }

    /**
     * @param $choiceGenerator
     * @return $this
     */
    public function setChoices($choiceGenerator): self
    {
        if (!is_array($choiceGenerator) && !is_callable($choiceGenerator)) {
            throw new InvalidArgumentException(sprintf('The argument of the "%s" method must be an array or a closure ("%s" given).', __METHOD__, gettype($choiceGenerator)));
        }

        $this->setCustomOption(self::OPTION_CHOICES, $choiceGenerator);

        return $this;
    }

    /**
     * @param $choiceGenerator
     * @return $this
     */
    public function setTranslatableChoices($choiceGenerator): self
    {
        $this->setChoices($choiceGenerator);
        $this->setCustomOption(self::OPTION_USE_TRANSLATABLE_CHOICES, true);

        return $this;
    }

    /**
     * @param bool $badgeSelector
     * @return $this
     */
    public function renderAsBadges(bool $badgeSelector = true): self
    {
        if (!is_bool($badgeSelector) && !is_array($badgeSelector) && !is_callable($badgeSelector)) {
            throw new InvalidArgumentException(sprintf('The argument of the "%s" method must be a boolean, an array or a closure ("%s" given).', __METHOD__, gettype($badgeSelector)));
        }

        if (is_array($badgeSelector)) {
            foreach ($badgeSelector as $badgeType) {
                if (!in_array($badgeType, self::VALID_BADGE_TYPES, true)) {
                    throw new InvalidArgumentException(sprintf('The values of the array passed to the "%s" method must be one of the following valid badge types: "%s" ("%s" given).', __METHOD__, implode(', ', self::VALID_BADGE_TYPES), $badgeType));
                }
            }
        }

        $this->setCustomOption(self::OPTION_RENDER_AS_BADGES, $badgeSelector);

        return $this;
    }

    /**
     * @param bool $asNative
     * @return $this
     */
    public function renderAsNativeWidget(bool $asNative = true): self
    {
        $this->setCustomOption(self::OPTION_WIDGET, $asNative ? self::WIDGET_NATIVE : self::WIDGET_AUTOCOMPLETE);

        return $this;
    }

    /**
     * @param bool $expanded
     * @return $this
     */
    public function renderExpanded(bool $expanded = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_EXPANDED, $expanded);

        return $this;
    }

    /**
     * @param bool $escape
     * @return $this
     */
    public function escapeHtml(bool $escape = true): self
    {
        $this->setCustomOption(self::OPTION_ESCAPE_HTML_CONTENTS, $escape);

        return $this;
    }
}
