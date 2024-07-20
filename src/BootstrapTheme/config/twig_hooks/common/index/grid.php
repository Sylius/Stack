<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('twig_hooks', [
        'hooks' => [
            'sylius_admin.common.index.content.grid' => [
                'filters' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/index/content/grid/filters.html.twig',
                ],
                'data_table' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/index/content/grid/data_table.html.twig',
                ],
            ],
        ],
    ]);
};
