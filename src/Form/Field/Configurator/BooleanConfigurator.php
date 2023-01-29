<?php

namespace App\Form\Field\Configurator;

use App\Form\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class BooleanConfigurator implements FieldConfiguratorInterface
{
    private AdminUrlGenerator $adminUrlGenerator;
    private ?CsrfTokenManagerInterface $csrfTokenManager;

    /**
     * @param AdminUrlGenerator $adminUrlGenerator
     * @param CsrfTokenManagerInterface|null $csrfTokenManager
     */
    public function __construct(AdminUrlGenerator $adminUrlGenerator, ?CsrfTokenManagerInterface $csrfTokenManager = null)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @return bool
     */
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return BooleanField::class === $field->getFieldFqcn();
    }

    /**
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @param AdminContext $context
     * @return void
     */
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $isRenderedAsSwitch = true === $field->getCustomOption(BooleanField::OPTION_RENDER_AS_SWITCH);

        if ($isRenderedAsSwitch) {
            $crudDto = $context->getCrud();

            if (null !== $crudDto && Action::NEW !== $crudDto->getCurrentAction()) {
                $toggleUrl = $this->adminUrlGenerator
                    ->setAction(Action::EDIT)
                    ->setEntityId($entityDto->getPrimaryKeyValue())
                    ->set('fieldName', $field->getProperty())
                    ->set('csrfToken', $this->csrfTokenManager?->getToken(BooleanField::CSRF_TOKEN_NAME))
                    ->generateUrl();
                $field->setCustomOption(BooleanField::OPTION_TOGGLE_URL, $toggleUrl);
            }

            $field->setFormTypeOptionIfNotSet('label_attr.class', 'checkbox-switch');
            $field->setCssClass($field->getCssClass().' has-switch');
        }
    }
}
