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
            'sylius_admin.common.update#title' => [
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

            'sylius_admin.common.update' => [
                'sidebar' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/sidebar.html.twig',
                ],
                'navbar' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/navbar.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content.html.twig',
                ],
                'footer' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/footer.html.twig',
                    'priority' => -100,
                ],
            ],

            'sylius_admin.common.update.content' => [
                'flashes' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/flashes.html.twig',
                ],
                'header' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header.html.twig',
                ],
                'form_error_alert' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/form_error_alert.html.twig',
                ],
                'form' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/form.html.twig',
                ],
            ],

            'sylius_admin.common.update.content.header' => [
                'breadcrumbs' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/update/content/header/breadcrumbs.html.twig',
                ],
                'title_block' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header/title_block.html.twig',
                ],
            ],

            'sylius_admin.common.update.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/update/content/header/title_block/title.html.twig',
                ],
                'actions' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/update/content/header/title_block/actions.html.twig',
                ],
            ],

            'sylius_admin.common.update.content.header.title_block.actions' => [
                'cancel' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header/title_block/actions/cancel.html.twig',
                    'priority' => -200,
                ],
                'update' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/header/title_block/actions/update.html.twig',
                    'priority' => -300,
                ],
            ],

            'sylius_admin.common.update.content.form' => [
                'sections' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/form/sections.html.twig',
                ],
            ],

            'sylius_admin.common.update.content.form.sections' => [
                'general' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/form/sections/general.html.twig',
                ],
            ],

            'sylius_admin.common.update.content.form.sections.general' => [
                'default' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/form/sections/general/default.html.twig',
                ],
            ],
        ],
    ]);
};
