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
    $container->extension('twig_hooks', [
        'hooks' => [
            'sylius_admin.base#stylesheets' => [
                'styles' => [
                    'template' => '@SyliusBootstrapTheme/shared/layout/stylesheets.html.twig',
                ],
            ],
            'sylius_admin.base#javascripts' => [
                'javascripts' => [
                    'template' => '@SyliusBootstrapTheme/shared/layout/javascripts.html.twig',
                ],
            ],
        ],
    ]);
};
