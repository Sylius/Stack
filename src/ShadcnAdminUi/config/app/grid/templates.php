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
                'apply_transition' => '@SyliusShadcnAdminUi/shared/grid/action/apply_transition.html.twig',
                'create' => '@SyliusShadcnAdminUi/shared/grid/action/create.html.twig',
                'delete' => '@SyliusShadcnAdminUi/shared/grid/action/delete.html.twig',
                'show' => '@SyliusShadcnAdminUi/shared/grid/action/show.html.twig',
                'update' => '@SyliusShadcnAdminUi/shared/grid/action/update.html.twig',
            ],
            'bulk_action' => [
                'delete' => '@SyliusShadcnAdminUi/shared/grid/bulk_action/delete.html.twig',
            ],
            'filter' => [
                'boolean' => '@SyliusShadcnAdminUi/shared/grid/filter/boolean.html.twig',
                'date' => '@SyliusShadcnAdminUi/shared/grid/filter/date.html.twig',
                'entity' => '@SyliusShadcnAdminUi/shared/grid/filter/entity.html.twig',
                'enum' => '@SyliusShadcnAdminUi/shared/grid/filter/enum.html.twig',
                'exists' => '@SyliusShadcnAdminUi/shared/grid/filter/exists.html.twig',
                'select' => '@SyliusShadcnAdminUi/shared/grid/filter/select.html.twig',
                'string' => '@SyliusShadcnAdminUi/shared/grid/filter/string.html.twig',
            ],
        ],
    ]);
};
