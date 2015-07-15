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

    public function getName()
    {
        return 'orangegate4_extension';
    }
}