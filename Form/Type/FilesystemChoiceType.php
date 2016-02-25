<?php

namespace Symbio\OrangeGate\AdminBundle\Form\Type;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilesystemChoiceType extends AbstractType
{

    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['base_path']) {
            throw new LogicException('The option "base_path" must be set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'base_path' => null,
        ));

        // Check if the choices already contain the empty value
        $view->vars['choices'] = $this->getChoicesForBasePath($options['base_path']);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'base_path' => null,
            'choices' => array(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'orangegate_filesystem_choice_type';
    }

    protected function getChoicesForBasePath($base_path)
    {
        $absolute_path = $this->kernel->getRootDir() . '/' . $base_path;
        $files = (new Finder())->directories()->depth(0)->in($absolute_path)->sortByName();

        $data = [];
        foreach ($files as $file) {
            $data[] = new ChoiceView($file->getFilename(), $file->getFilename(), $file->getFilename());
        }

        return $data;
    }
}
