<?php

namespace Symbio\OrangeGate\AdminBundle\Twig;

class OrangeGate4Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('preg_replace', array($this, 'pregReplaceFilter')),
            new \Twig_SimpleFilter('preg_match', array($this, 'pregMatchFilter')),
        );
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

    public function pregMatchFilter($subject, $pattern, $replacement='', $limit=-1)
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