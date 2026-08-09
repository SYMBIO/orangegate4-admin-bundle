<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Minimal Sonata Block 5 port of the legacy OrangeGate formatter block.
 */
final class FormatterBlockService extends AbstractBlockService
{
    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        return $this->renderResponse($blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
            'content' => $blockContext->getBlock()->getSetting('content'),
        ], $response);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'content' => '',
            'format' => 'richhtml',
            'template' => '@SonataFormatter/Block/block_formatter.html.twig',
        ]);
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function configureEditForm(FormMapper $form, BlockInterface $block): void
    {
    }

    public function configureCreateForm(FormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }
}
