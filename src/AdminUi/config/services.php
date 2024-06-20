<?php

/*
 * This file is part of SyliusFixturesPlugin.
 *
 * (c) Akawaka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\AdminUi\Menu\MenuBuilder;
use Sylius\AdminUi\Menu\MenuBuilderInterface;
use Sylius\AdminUi\TwigHooks\Hookable\Metadata\RoutingHookableMetadataFactory;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_admin_ui.menu_builder', MenuBuilder::class)
        ->args([service('knp_menu.factory')])
        ->tag(name: 'knp_menu.menu_builder', attributes: ['method' => 'createMenu', 'alias' => 'sylius.ui.menu.admin'])
    ;
    $services->alias(MenuBuilderInterface::class, 'sylius_admin_ui.menu_builder');

    $services->set('sylius_admin_ui.factory.hookable_metadata', RoutingHookableMetadataFactory::class)
        ->decorate('twig_hooks.factory.hookable_metadata')
        ->args([
            service('.inner'),
            param('sylius_admin_ui.routing')
        ])
    ;
};
