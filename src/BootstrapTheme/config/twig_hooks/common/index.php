<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('twig_hooks', [
        'hooks' => [
            'sylius_admin.common.index' => [
                'sidebar' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/sidebar.html.twig'
                ],
                'navbar' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/navbar.html.twig'
                ],
                'content' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/content.html.twig',
                ]
            ],
            'sylius_admin.common.index.content' => [
                'header' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/content/header.html.twig',
                ],
                'grid' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/index/content/grid.html.twig',
                ],
            ],
            'sylius_admin.common.index.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/index/content/header/breadcrumbs.html.twig',
                ],
                'title_block' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/content/header/title_block.html.twig',
                ],

            ],
            'sylius_admin.common.index.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/content/header/title_block/title.html.twig',
                ],
                'actions' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/content/header/title_block/actions.html.twig',
                ],
            ],
        ],
    ]);
};
