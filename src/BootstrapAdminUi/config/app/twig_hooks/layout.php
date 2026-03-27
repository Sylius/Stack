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
                    'template' => '@SyliusBootstrapAdminUi/shared/layout/title.html.twig',
                ],
            ],

            'sylius_admin.base#stylesheets' => [
                'theme' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/layout/theme.html.twig',
                    'priority' => 200,
                ],
                'styles' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/layout/stylesheets.html.twig',
                ],
                'symfony_ux' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/layout/symfony_ux_styles.html.twig',
                ],
            ],

            'sylius_admin.base#javascripts' => [
                'javascripts' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/layout/javascripts.html.twig',
                ],
                'symfony_ux' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/layout/symfony_ux_scripts.html.twig',
                ],
            ],
        ],
    ]);
};
