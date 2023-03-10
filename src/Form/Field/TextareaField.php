<?php

namespace App\Form\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class TextareaField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_MAX_LENGTH = TextField::OPTION_MAX_LENGTH;
    public const OPTION_NUM_OF_ROWS = 'numOfRows';
    public const OPTION_RENDER_AS_HTML = TextField::OPTION_RENDER_AS_HTML;
    public const OPTION_STRIP_TAGS = TextField::OPTION_STRIP_TAGS;

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
            ->setTemplateName('crud/field/textarea')
            ->setFormType(TextareaType::class)
            ->addCssClass('field-textarea')
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-textarea.js')->onlyOnForms())
            ->setColumns('col-6')
            ->setCustomOption(self::OPTION_MAX_LENGTH, null)
            ->setCustomOption(self::OPTION_NUM_OF_ROWS, 5)
            ->setCustomOption(self::OPTION_RENDER_AS_HTML, false)
            ->setCustomOption(self::OPTION_STRIP_TAGS, false);
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setMaxLength(int $length): self
    {
        if ($length < 1) {
            throw new InvalidArgumentException(sprintf('The argument of the "%s()" method must be 1 or higher (%d given).', __METHOD__, $length));
        }

        $this->setCustomOption(self::OPTION_MAX_LENGTH, $length);

        return $this;
    }

    /**
     * @param int $rows
     * @return $this
     */
    public function setNumOfRows(int $rows): self
    {
        if ($rows < 1) {
            throw new InvalidArgumentException(sprintf('The argument of the "%s()" method must be 1 or higher (%d given).', __METHOD__, $rows));
        }

        $this->setCustomOption(self::OPTION_NUM_OF_ROWS, $rows);

        return $this;
    }

    /**
     * @param bool $asHtml
     * @return $this
     */
    public function renderAsHtml(bool $asHtml = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_AS_HTML, $asHtml);

        return $this;
    }

    /**
     * @param bool $stripTags
     * @return $this
     */
    public function stripTags(bool $stripTags = true): self
    {
        $this->setCustomOption(self::OPTION_STRIP_TAGS, $stripTags);

        return $this;
    }
}
