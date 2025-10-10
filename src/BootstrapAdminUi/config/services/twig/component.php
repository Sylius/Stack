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

use Sylius\BootstrapAdminUi\Twig\Component\UserDropdownComponent;
use Sylius\UXLiveComponentExtra\Twig\Component\Grid\DataTableComponent;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_bootstrap_admin_ui.twig.component.navbar.user_dropdown', UserDropdownComponent::class)
        ->public()
        ->args([
            param('sylius_admin_ui.routing'),
            service('security.token_storage'),
            service('router'),
        ])
        ->tag('twig.component', [
            'key' => 'sylius_bootstrap_admin_ui:navbar:user_dropdown',
            'template' => '@SyliusBootstrapAdminUi/shared/components/navbar/user.html.twig',
        ]);
};
