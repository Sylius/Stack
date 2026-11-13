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

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.common.component.sidebar' => [
                'logo' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/sidebar/logo.html.twig',
                ],
                'menu' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/sidebar/menu.html.twig',
                ],
            ],

            'sylius_admin.common.component.sidebar.logo' => [
                'image' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/sidebar/logo/image.html.twig',
                    'configuration' => [
                        'name' => 'Sylius',
                        'subtitle' => 'Admin panel',
                    ],
                ],
            ],
        ],
    ]);
};
