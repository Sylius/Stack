<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('twig_hooks', [
        'hooks' => [
            'sylius_admin.common.component.navbar' => [
                'menu' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/navbar/menu.html.twig',
                ],
                'items' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/navbar/items.html.twig',
                ],
            ],
            'sylius_admin.common.component.navbar.items' => [
                'user_dropdown' => [
                    'template' => '@SyliusBootstrapTheme/shared/crud/common/navbar/items/user.html.twig',
                ],
            ],
        ],
    ]);
};
