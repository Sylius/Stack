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
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/title/current.html.twig',
                    'priority' => 200,
                ],
                'separator' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/title/separator.html.twig',
                    'priority' => 100,
                ],
                'parent' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/title/parent.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_admin.common.index' => [
                'sidebar' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/sidebar.html.twig',
                ],
                'navbar' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/navbar.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content.html.twig',
                ],
            ],

            'sylius_admin.common.index.content' => [
                'flashes' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/flashes.html.twig',
                ],
                'header' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header.html.twig',
                ],
                'grid' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid.html.twig',
                ],
                'footer' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/footer.html.twig',
                    'priority' => -100,
                ],
            ],

            'sylius_admin.common.index.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/header/breadcrumbs.html.twig',
                ],
                'title_block' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header/title_block.html.twig',
                ],
            ],

            'sylius_admin.common.index.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header/title_block/title.html.twig',
                ],
                'actions' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header/title_block/actions.html.twig',
                ],
            ],

            'sylius_admin.common.index.content.grid' => [
                'filters' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid/filters.html.twig',
                ],
                'data_table' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid/data_table.html.twig',
                ],
                'no_data_block' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid/no_results.html.twig',
                ],
            ],

            'sylius_admin.common.index.content.grid.no_results' => [
                'image' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid/no_results/image.html.twig',
                ],
                'title' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid/no_results/title.html.twig',
                ],
                'subtitle' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid/no_results/subtitle.html.twig',
                ],
                'action' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/index/content/grid/no_results/action.html.twig',
                ],
            ],
        ],
    ]);
};
