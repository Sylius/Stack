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
            'sylius_admin.common.index#title' => [
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

            'sylius_admin.common.index' => [
                'sidebar' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar.html.twig',
                ],
                'navbar' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/navbar.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content.html.twig',
                ],
            ],

            'sylius_admin.common.index.content' => [
                'flashes' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/flashes.html.twig',
                ],
                'header' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header.html.twig',
                ],
                'grid' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid.html.twig',
                ],
                'footer' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/footer.html.twig',
                    'priority' => -100,
                ],
            ],

            'sylius_admin.common.index.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/header/breadcrumbs.html.twig',
                ],
                'title_block' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block.html.twig',
                ],
            ],

            'sylius_admin.common.index.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block/title.html.twig',
                ],
                'actions' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/common/content/header/title_block/actions.html.twig',
                ],
            ],

            'sylius_admin.common.index.content.grid' => [
                'filters' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/filters.html.twig',
                ],
                'data_table' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/data_table.html.twig',
                ],
                'no_data_block' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/no_results.html.twig',
                ],
            ],

            'sylius_admin.common.index.content.grid.no_results' => [
                'image' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/no_results/image.html.twig',
                ],
                'title' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/no_results/title.html.twig',
                ],
                'subtitle' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/no_results/subtitle.html.twig',
                ],
                'action' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/no_results/action.html.twig',
                ],
            ],
        ],
    ]);
};
