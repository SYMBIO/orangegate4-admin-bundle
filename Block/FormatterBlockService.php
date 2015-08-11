<?php

namespace Symbio\OrangeGate\AdminBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symbio\OrangeGate\PageBundle\Entity\Block;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FormatterBlockService extends \Sonata\FormatterBundle\Block\FormatterBlockService
{
    protected $twig;

    /**
     * @param string $name
     * @param EngineInterface $templating
     * @param TranslatorInterface $translator
     */
    public function __construct($name, EngineInterface $templating, TranslatorInterface $translator, \Twig_Environment $twig)
    {
        $this->translator = $translator;
        $this->twig = clone $twig;
        $this->twig->setLoader(new \Twig_Loader_String());
        parent::__construct($name, $templating);

    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $content = $blockContext->getBlock()->getSetting('content');

        $twig_content = $content ? $this->twig->render($content) : $content;

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $blockContext->getSettings(),
            'content'   => $twig_content,
        ), $response);
    }

    /**
    * {@inheritdoc}
    */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        if ($block instanceof Block && $block->getPage()) {
            /**
             * @var \Doctrine\ORM\PersistentCollection $translations
             */
            $translations = $block->getPage()->getTranslations();
            $locales = $translations->getKeys();
        } else {
            $locales = array();
        }

        $formMapper->add('translations', 'orangegate_translations', array(
            'label' => false,
            'locales' => $locales,
            'fields' => array(
                'enabled' => array(
                    'field_type' => 'checkbox',
                    'required' => false,
                ),
				'settings' => array(
					'field_type' => 'sonata_type_immutable_array',
                    'label' => false,
					'keys' => array(
                        array('content', 'orangegate_simple_formatter_type', array(
                                'ckeditor_context' => 'formatter',
                            )
                        ),
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
