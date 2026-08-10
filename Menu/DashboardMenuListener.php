<?php

declare(strict_types=1);

namespace Symbio\OrangeGate\AdminBundle\Menu;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ConfigureMenuEvent::SIDEBAR)]
final class DashboardMenuListener
{
    public function __invoke(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild(
            $event->getFactory()->createItem('dashboard', [
                'route' => 'sonata_admin_dashboard',
                'label' => 'dashboard',
                'extras' => [
                    'icon' => 'fa fa-dashboard',
                    'on_top' => true,
                    'translation_domain' => 'SymbioOrangeGateAdminBundle',
                    'sonata_admin' => true,
                ],
            ]),
            ['first' => true]
        );
    }
}
