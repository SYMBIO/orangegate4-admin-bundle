<?php

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatePickerType extends \Sonata\CoreBundle\Form\Type\DatePickerType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orangegate_type_date_picker';
    }
}