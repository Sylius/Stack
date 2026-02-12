<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminUi\Knp\Menu\MenuBuilder;
use Sylius\AdminUi\Knp\Menu\MenuBuilderInterface;
use Sylius\AdminUi\TwigHooks\Hookable\Metadata\RoutingHookableMetadataFactory;

return function (ContainerConfigurator $configurator): void {
    $configurator->import('./services/**/**.php');

    $services = $configurator->services();

    $services->set('sylius_admin_ui.knp.menu_builder', MenuBuilder::class)
        ->args([service('knp_menu.factory'), service('event_dispatcher')])
        ->tag(name: 'knp_menu.menu_builder', attributes: ['method' => 'createMenu', 'alias' => 'sylius_admin_ui.menu.sidebar'])
    ;
    $services->alias(MenuBuilderInterface::class, 'sylius_admin_ui.knp.menu_builder');

    $services->set('sylius_admin_ui.twig_hooks.factory.hookable_metadata', RoutingHookableMetadataFactory::class)
        ->decorate('sylius_twig_hooks.factory.hookable_metadata')
        ->args([
            service('.inner'),
            param('sylius_admin_ui.routing'),
        ])
    ;
};
