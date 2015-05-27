<?php

namespace Symbio\OrangeGate\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SymbioOrangeGateAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}
