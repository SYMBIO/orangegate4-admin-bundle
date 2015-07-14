<?php

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symbio\OrangeGate\AdminBundle\Date\MomentFormatConverter;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimePickerType extends \Sonata\CoreBundle\Form\Type\DateTimePickerType
{

    /**
     * @var MomentFormatConverter
     */
    private $formatConverter;

    /**
     * @param MomentFormatConverter $formatConverter
     */
    public function __construct(MomentFormatConverter $formatConverter)
    {
        $this->formatConverter = $formatConverter;
        parent::__construct($formatConverter);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orangegate_type_datetime_picker';
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        $view->vars['dp_options']['format'] = $view->vars['moment_format'];
    }
}