<?php

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

class DynamicChoiceType extends ChoiceType
{

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $options['expanded'] = false; // only select can be dynamic

        if ($options['source_field']) {
            $value = $form->getParent()[$options['source_field']]->getData();
            if (is_object($value)) {
                $value = $value->getId();
            }
            $view->vars['source_field'] = $options['source_field'];

            $completeChoices = array();
            foreach ($options['choices'] as $key => $val) {
                $completeChoices[$key] = array();
                foreach ($val as $k => $v) {
                    $completeChoices[$key][] = array('id' => $k, 'text' => $v);
                }
            }
            $view->vars['complete_choices'] = $completeChoices;

            $choices = array();
            if (isset($options['choices'][$value])) {
                $choices = $options['choices'][$value];
            }

            $options['choice_list'] = new SimpleChoiceList($choices);
        }

        parent::buildView($view, $form, $options);
    }

    public function getName()
    {
        return 'orangegate_type_dynamic_choice';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'source_field'      => null, // string or property path
        ));
    }
}
