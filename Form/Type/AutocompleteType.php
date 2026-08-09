<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class AutocompleteType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $property = $form->getName();
        $fieldDescription = $options['sonata_field_description'] ?? null;
        if (null === $fieldDescription) {
            $view->vars['choices'] = [];

            return;
        }

        $admin = $fieldDescription->getAdmin();
        $modelManager = $admin->getModelManager();
        $class = $admin->getClass();

        $entities = $modelManager
            ->getEntityManager($class)
            ->getRepository($class)
            ->findBy([], [$property => 'ASC']);

        $accessor = PropertyAccess::createPropertyAccessor();
        $choices = [];
        foreach ($entities as $entity) {
            $value = $accessor->getValue($entity, $property);
            $choices[$value] = $value;
        }

        $view->vars['choices'] = $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'sonata_field_description' => null,
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'orangegate_type_autocomplete';
    }
}
