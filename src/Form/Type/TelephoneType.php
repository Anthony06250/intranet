<?php

namespace App\Form\Type;

use App\Form\DataTransformer\TelephoneTransformer;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TelephoneType extends AbstractType
{
    /**
     * @param RequestStack $request
     * @param PhoneNumberUtil $phoneNumberUtil
     */
    public function __construct(private readonly RequestStack $request,
                                private readonly PhoneNumberUtil $phoneNumberUtil)
    {
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new TelephoneTransformer($this->request, $this->phoneNumberUtil));
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'tel';
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return TextType::class;
    }
}