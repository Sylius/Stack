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
            'sylius_admin.common.show#title' => [
                'current' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/title/current.html.twig',
                    'priority' => 200,
                ],
                'separator' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/title/separator.html.twig',
                    'priority' => 100,
                ],
                'parent' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/title/parent.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_admin.common.show' => [
                'sidebar' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar.html.twig',
                ],
                'navbar' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/navbar.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content.html.twig',
                ],
                'footer' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/footer.html.twig',
                    'priority' => -100,
                ],
            ],

            'sylius_admin.common.show.content' => [
                'flashes' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/flashes.html.twig',
                ],
                'header' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header.html.twig',
                ],
            ],

            'sylius_admin.common.show.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/show/content/header/breadcrumbs.html.twig',
                ],
                'title_block' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block.html.twig',
                ],
            ],

            'sylius_admin.common.show.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/show/content/header/title_block/title.html.twig',
                ],
            ],
        ],
    ]);
};
