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
            'sylius_front.account.dashboard.index.content' => [
                'main_section' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/main_section.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.dashboard.index.content.header' => [
                'title' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/header/title.html.twig',
                    'priority' => 100,
                ],
                'subtitle' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/header/subtitle.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.dashboard.index.content.main_section' => [
                'user_info' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/main_section/user_info.html.twig',
                    'priority' => 100,
                ],
                'buttons' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/main_section/buttons.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.dashboard.index.content.main_section.user_info' => [
                'user_details' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/main_section/user_info/details.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.dashboard.index.content.main_section.user_info.details' => [
                'user_identifier' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/main_section/user_info/details/user_identifier.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.dashboard.index.content.main_section.buttons' => [
                'edit' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/main_section/buttons/edit.html.twig',
                    'priority' => 200,
                ],
                'change_password' => [
                    'template' => '@SyliusBootstrapFrontUi/account/dashboard/index/content/main_section/buttons/change_password.html.twig',
                    'priority' => 100,
                ],
            ],
        ],
    ]);
};
