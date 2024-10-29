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
    $routes->add('sylius_front_ui_account_dashboard', '/dashboard')
        ->controller(TemplateController::class)
        ->defaults(['template' => '@SyliusFrontUi/account/dashboard.html.twig'])
    ;

    $routes->add('sylius_front_ui_account_profile_update', '/profile/edit')
        ->methods(['GET', 'POST'])
        ->controller(TemplateController::class)
        ->defaults(['template' => '@SyliusFrontUi/account/profile_update.html.twig'])
    ;

    $routes->add('sylius_front_ui_account_change_password', '/change_password')
        ->methods(['GET', 'POST'])
        ->controller('sylius_front_ui.controller.account.change_password')
        ->defaults(['template' => '@SyliusFrontUi/account/change_password.html.twig'])
    ;
};
