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
                'create' => '@SyliusBootstrapAdminUi/shared/grid/action/create.html.twig',
                'delete' => '@SyliusBootstrapAdminUi/shared/grid/action/delete.html.twig',
                'show' => '@SyliusBootstrapAdminUi/shared/grid/action/show.html.twig',
                'update' => '@SyliusBootstrapAdminUi/shared/grid/action/update.html.twig',
            ],
            'bulk_action' => [
                'delete' => '@SyliusBootstrapAdminUi/shared/grid/bulk_action/delete.html.twig',
            ],
            'filter' => [
                'boolean' => '@SyliusBootstrapAdminUi/shared/grid/filter/boolean.html.twig',
                'date' => '@SyliusBootstrapAdminUi/shared/grid/filter/date.html.twig',
                'entity' => '@SyliusBootstrapAdminUi/shared/grid/filter/entity.html.twig',
                'exists' => '@SyliusBootstrapAdminUi/shared/grid/filter/exists.html.twig',
                'select' => '@SyliusBootstrapAdminUi/shared/grid/filter/select.html.twig',
                'string' => '@SyliusBootstrapAdminUi/shared/grid/filter/string.html.twig',
                'enum' => '@SyliusBootstrapAdminUi/shared/grid/filter/enum.html.twig',
            ],
        ],
    ]);
};
