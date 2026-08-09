<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Legacy simple formatter field — maps to FOS CKEditor.
 */
final class SimpleFormatterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'format' => 'richhtml',
            'ckeditor_context' => 'default',
        ]);
    }

    public function getParent(): string
    {
        return CKEditorType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'orangegate_type_simple_formatter';
    }
}
