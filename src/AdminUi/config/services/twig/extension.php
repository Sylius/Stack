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

use Sylius\AdminUi\Twig\Extension\ContextExtension;
use Sylius\AdminUi\Twig\Extension\RedirectPathExtension;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_admin_ui.twig.extension.redirect_path', RedirectPathExtension::class)
        ->args([
            service('router'),
            service('sylius.grid.filter_storage')->nullOnInvalid(),
        ])
        ->tag(name: 'twig.extension')
    ;

    $services->set('sylius_admin_ui.twig.extension.context', ContextExtension::class)
        ->args([
            service('sylius_admin_ui.context'),
        ])
        ->tag(name: 'twig.extension')
    ;
};
