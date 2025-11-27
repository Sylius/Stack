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

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('sylius_admin_ui_login', '/login')
        ->controller('sylius_admin_ui.controller.login')
    ;

    $routes->add('sylius_admin_ui_login_check', '/login_check');
    $routes->add('sylius_admin_ui_logout', '/logout')
        ->methods(['GET'])
    ;

    $routes->add('sylius_admin_ui_dashboard', '/')
        ->controller(TemplateController::class)
        ->defaults([
            'template' => '@SyliusAdminUi/dashboard/index.html.twig',
        ])
    ;

    $routes->add('sylius_admin_ui_live_component', '/_components/{_live_component}/{_live_action}')
        ->defaults([
            '_live_action' => 'get',
        ])
    ;
};
