<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;

final class OrangeGate4Extension extends AbstractExtension
{
    public function getName(): string
    {
        return 'orangegate4';
    }
}
