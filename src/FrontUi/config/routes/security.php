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
    $routes->add('sylius_front_ui_login', '/login')
        ->controller('sylius_front_ui.controller.login')
    ;

    $routes->add('sylius_front_ui_login_check', '/login_check');
    $routes->add('sylius_front_ui_logout', '/logout')
        ->methods(['GET'])
    ;

    $routes->add('sylius_front_ui_register', '/register')
        ->controller(TemplateController::class)
        ->defaults([
            'template' => '@SyliusFrontUi/security/register.html.twig',
        ])
    ;
};
