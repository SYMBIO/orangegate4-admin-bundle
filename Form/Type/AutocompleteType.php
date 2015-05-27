<?php

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AutocompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $property = $form->getName();
        $admin = $options['sonata_field_description']->getAdmin();
        $modelManager = $admin->getModelManager();
        $class = $admin->getClass();

        $entities = $modelManager
                        ->getEntityManager($class)
                        ->getRepository($class)
                        ->findBy(array(), array($property => 'ASC'));

        $accessor = PropertyAccess::createPropertyAccessor();

        $choices = array();
        foreach ($entities as $entity) {
            $value = $accessor->getValue($entity, $property);
            $choices[$value] = $value;
        }

        $view->vars['choices'] = $choices;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound'          => false,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'orangegate_type_autocomplete';
    }
}
