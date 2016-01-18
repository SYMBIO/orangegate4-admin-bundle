<?php

namespace Symbio\OrangeGate\AdminBundle\Twig;

class OrangeGate4Extension extends \Twig_Extension
{

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('preg_match', array($this, 'pregMatchFunction')),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('preg_replace', array($this, 'pregReplaceFilter')),
            new \Twig_SimpleFilter('html_decode', array($this, 'htmlDecodeFilter')),
            new \Twig_SimpleFilter('hard_spaces', array($this, 'hardSpacesFilter')),
            new \Twig_SimpleFilter('evaluate', array($this, 'evaluateFilter'), array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true
                )
            )),
            new \Twig_SimpleFilter('wysiwyg', array($this, 'wysiwygFilter'), array(
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => array(
                    'evaluate' => true
                )
            )),
        );
    }

    public function htmlDecodeFilter($string)
    {
        return strip_tags(html_entity_decode($string));
    }

    public function pregReplaceFilter($subject, $pattern, $replacement='', $limit=-1)
    {
        if (!isset($subject)) {
            return null;
        }
        else {
            return preg_replace($pattern, $replacement, $subject, $limit);
        }
    }

    public function pregMatchFunction($subject, $pattern, $replacement='', $limit=-1)
    {
        if (!isset($subject)) {
            return null;
        }
        else {
            preg_match($pattern, $subject, $matches);

            return $matches;
        }
    }

    /**
     * Replace soft space before one-char expression by hard space
     *
     * @param string $value
     * @param int $limit
     * @return string
     */
    public function hardSpacesFilter($string)
    {
        if (empty($string)) return null;
        $pattern = '/(\s[\[\]\(\){},\.\+\-:;!\?"\'§&"\/\\\]?\w[\[\]\(\){},\.\+\-:;!\?"\'§&"\/\\\]?)\s(\S+)/';
        $replacement = '$1&nbsp;$2';
        return preg_replace($pattern, $replacement, $string);
    }

    /**
     * Evaluate $string through the $environment and return its results
     *
     * @oaram \Twig_Environment $environment
     * @param array $context
     * @param string $string
     *
     * @return string
     */
    public function evaluateFilter(\Twig_Environment $environment, $context, $string)
    {
        $loader = $environment->getLoader();
        $parsed = $this->parseString($environment, $context, $string);
        $environment->setLoader($loader);
        return $parsed;
    }

    /**
     * Aggregates filters suitable for wysiwyg
     *
     * @oaram \Twig_Environment $environment
     * @param array $context
     * @param string $string
     *
     * @return string
     */
    public function wysiwygFilter(\Twig_Environment $environment, $context, $string)
    {
        $parsed = $this->evaluateFilter($environment, $context, $string);
        $parsed = $this->hardSpacesFilter($parsed);
        return $parsed;
    }

    /**
     * Sets the parser for the environment to Twig_Loader_String, and parsed the string $string.
     *
     * @param \Twig_Environment $environment
     * @param array $context
     * @param string $string
     * @return string
     */
    protected function parseString(\Twig_Environment $environment, $context, $string)
    {
        $environment->setLoader(new \Twig_Loader_String());
        return $environment->render($string, $context);
    }

    public function getName()
    {
        return 'orangegate4_extension';
    }
}