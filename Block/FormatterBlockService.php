<?php

namespace Symbio\OrangeGate\AdminBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormatterBlockService extends \Sonata\FormatterBundle\Block\FormatterBlockService
{

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $blockContext->getSettings()
        ), $response);
    }

    /**
    * {@inheritdoc}
    */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('translations', 'orangegate_translations', array(
            'label' => false,
            'locales' => array('cs', 'en', 'de'),
            'fields' => array(
                'enabled' => array(
                    'field_type' => 'checkbox',
                    'required' => false,
                ),
				'settings' => array(
					'field_type' => 'sonata_type_immutable_array',
                    'label' => false,
					'keys' => array(
                        array('content', 'sonata_formatter_type', function(FormBuilderInterface $formBuilder) {
                            return array(
                                'event_dispatcher' => $formBuilder->getEventDispatcher(),
                                'format_field'     => array('format', '[format]'),
                                'source_field'     => array('rawContent', '[rawContent]'),
                                'target_field'     => '[content]',
                                'ckeditor_context' => 'formatter',
                            );
                        }),
                    )
                )
            )
        ));
    }

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'Rich Html Text Area';
	}

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'format'     => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content'    => '<b>Insert your custom content here</b>',
            'template'   => 'SymbioOrangeGateAdminBundle:Block:block_formatter.html.twig'
        ));
    }
}
