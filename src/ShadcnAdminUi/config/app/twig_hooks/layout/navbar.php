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
            'sylius_admin.common.component.navbar' => [
                'sidebar_trigger' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/navbar/sidebar_trigger.html.twig',
                    'priority' => 100,
                ],
                'menu' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/navbar/menu.html.twig',
                ],
                'items' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/navbar/items.html.twig',
                ],
            ],

            'sylius_admin.common.component.navbar.items' => [
                'theme_switcher' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/navbar/items/theme_switcher.html.twig',
                ],
                'user' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/navbar/items/user.html.twig',
                ],
            ],
        ],
    ]);
};
