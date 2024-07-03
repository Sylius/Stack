<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_grid', [
        'templates' => [
            'action' => [
                'apply_transition' => '@SyliusBootstrapTheme/shared/grid/action/apply_transition.html.twig',
//                    'archive' => '@SyliusUi/Grid/Action/archive.html.twig',
                'create' => '@SyliusBootstrapTheme/shared/grid/action/create.html.twig',
                'default' => '@SyliusBootstrapTheme/shared/grid/action/default.html.twig',
                'delete' => '@SyliusBootstrapTheme/shared/grid/action/delete.html.twig',
//                    'links' => '@SyliusUi/Grid/Action/links.html.twig',
                'update' => '@SyliusBootstrapTheme/shared/grid/action/update.html.twig',
//                    'show' => '@SyliusUi/Grid/Action/show.html.twig',
            ],
            'filter' => [
                'string' => '@SyliusBootstrapTheme/shared/grid/filter/_string.html.twig',
                'boolean' => '@SyliusBootstrapTheme/shared/grid/filter/_boolean.html.twig',
                'date' => '@SyliusBootstrapTheme/shared/grid/filter/_date.html.twig',
                'entity' => '@SyliusBootstrapTheme/shared/grid/filter/_entity.html.twig',
                'money' => '@SyliusBootstrapTheme/shared/grid/filter/_money.html.twig',
                'exists' => '@SyliusBootstrapTheme/shared/grid/filter/_exists.html.twig',
                'select' => '@SyliusBootstrapTheme/shared/grid/filter/_select.html.twig',
            ],
            'bulk_action' => [
                'delete' => '@SyliusBootstrapTheme/shared/grid/bulk_action/_delete.html.twig',
            ],
        ],
    ]);
};
