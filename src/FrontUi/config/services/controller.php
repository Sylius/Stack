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

use Sylius\FrontUi\Symfony\Controller\Account\ChangePasswordController;
use Sylius\FrontUi\Symfony\Controller\LoginController;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_front_ui.controller.login', LoginController::class)
        ->public()
        ->args([
            service('security.authentication_utils'),
            service('form.factory'),
            service('twig'),
        ])
    ;
    $services->alias(LoginController::class, 'sylius_front_ui.controller.login');

    $services->set('sylius_front_ui.controller.account.change_password', ChangePasswordController::class)
        ->public()
        ->args([
            service('security.token_storage'),
            service('form.factory'),
            service('twig'),
            service('event_dispatcher'),
            service('router'),
            service('request_stack'),
        ])
    ;
};
