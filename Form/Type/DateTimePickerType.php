<?php

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimePickerType extends \Sonata\CoreBundle\Form\Type\DateTimePickerType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orangegate_type_datetime_picker';
    }
}