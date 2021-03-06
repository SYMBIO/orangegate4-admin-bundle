<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Ivory\CKEditorBundle\Model\ConfigManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleFormatterType extends AbstractType
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * Constructor.
     *
     * @param ConfigManagerInterface $configManager An Ivory CKEditor bundle configuration manager
     */
    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $ckeditorConfiguration = array(
            'toolbar' => array_values($options['ckeditor_toolbar_icons']),
        );

        if ($options['ckeditor_context']) {
            $contextConfig = $this->configManager->getConfig($options['ckeditor_context']);
            $ckeditorConfiguration = array_merge($ckeditorConfiguration, $contextConfig);
        }

        $view->vars['ckeditor_configuration'] = $ckeditorConfiguration;
        $view->vars['ckeditor_basepath'] = $options['ckeditor_basepath'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'ckeditor_toolbar_icons'    => array(array(
                'Bold', 'Italic', 'Underline',
                '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord',
                '-', 'Undo', 'Redo',
                '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
                '-', 'Blockquote',
                '-', 'Image', 'Link', 'Unlink', 'Table', ),
                array('Maximize', 'Source'),
            ),
            'ckeditor_basepath'   => 'bundles/sonataformatter/vendor/ckeditor',
            'ckeditor_context'    => null,
            'format_options'      => array(
                'attr' => array(
                    'class' => 'span10 col-sm-10 col-md-10',
                    'rows'  => 20,
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orangegate_simple_formatter_type';
    }
}
