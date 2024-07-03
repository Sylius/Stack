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
                'boolean' => '@SyliusBootstrapTheme/shared/grid/filter/boolean.html.twig',
                'date' => '@SyliusBootstrapTheme/shared/grid/filter/date.html.twig',
                'entity' => '@SyliusBootstrapTheme/shared/grid/filter/entity.html.twig',
                'exists' => '@SyliusBootstrapTheme/shared/grid/filter/exists.html.twig',
                'money' => '@SyliusBootstrapTheme/shared/grid/filter/money.html.twig',
                'select' => '@SyliusBootstrapTheme/shared/grid/filter/select.html.twig',
                'string' => '@SyliusBootstrapTheme/shared/grid/filter/string.html.twig',
            ],
            'bulk_action' => [
                'delete' => '@SyliusBootstrapTheme/shared/grid/bulk_action/delete.html.twig',
            ],
        ],
    ]);
};
