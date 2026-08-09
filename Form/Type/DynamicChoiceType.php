<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Choice type whose options depend on another field (legacy dynamic select).
 */
final class DynamicChoiceType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['source_field'] = $options['source_field'];
        $view->vars['complete_choices'] = [];

        $allChoices = $options['choices'] ?? [];
        foreach ($allChoices as $key => $val) {
            if (!\is_array($val)) {
                continue;
            }
            $view->vars['complete_choices'][$key] = [];
            foreach ($val as $k => $v) {
                $view->vars['complete_choices'][$key][] = ['id' => $k, 'text' => $v];
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'source_field' => null,
            'choices' => [],
        ]);
        $resolver->setAllowedTypes('source_field', ['null', 'string']);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'orangegate_type_dynamic_choice';
    }
}
