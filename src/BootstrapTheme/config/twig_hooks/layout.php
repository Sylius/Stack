<?php

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