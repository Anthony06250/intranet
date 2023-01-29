<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MonthType extends AbstractType
{
    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['type'] = 'month';
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'month';
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return TextType::class;
    }
}