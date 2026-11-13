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
            'sylius_admin.base#base_title' => [
                'default' => [
                    'template' => '@SyliusShadcnAdminUi/shared/layout/title.html.twig',
                ],
            ],

            'sylius_admin.base#metatags' => [
                'color_scheme' => [
                    'template' => '@SyliusShadcnAdminUi/shared/layout/metatags.html.twig',
                ],
            ],

            'sylius_admin.base#stylesheets' => [
                'theme' => [
                    'template' => '@SyliusShadcnAdminUi/shared/layout/theme.html.twig',
                    'priority' => 200,
                ],
                'themes' => [
                    'template' => '@SyliusShadcnAdminUi/shared/layout/themes.html.twig',
                    'priority' => 100,
                ],
                'styles' => [
                    'template' => '@SyliusShadcnAdminUi/shared/layout/stylesheets.html.twig',
                    'configuration' => [
                        'path' => 'styles/admin.css',
                    ],
                ],
            ],

            'sylius_admin.base#javascripts' => [
                'javascripts' => [
                    'template' => '@SyliusShadcnAdminUi/shared/layout/javascripts.html.twig',
                    'configuration' => [
                        'entrypoint' => 'admin',
                    ],
                ],
            ],
        ],
    ]);
};
