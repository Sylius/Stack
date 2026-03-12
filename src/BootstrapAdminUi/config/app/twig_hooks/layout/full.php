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
            'sylius_admin.common.layout.full#sidebar' => [
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar.html.twig',
                ],
            ],
            'sylius_admin.common.layout.full#navbar' => [
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/navbar.html.twig',
                ],
            ],
            'sylius_admin.common.layout.full#footer' => [
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/footer.html.twig',
                ],
            ],
        ],
    ]);
};
