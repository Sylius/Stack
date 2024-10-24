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
            'sylius_front.base#stylesheets' => [
                'styles' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/styles.html.twig',
                ],
            ],

            'sylius_front.base#javascripts' => [
                'scripts' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/scripts.html.twig',
                ],
            ],

            'sylius_front.base.header' => [
                'content' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content.html.twig',
                ],
                'navbar' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/navbar.html.twig',
                ],
                'flashes' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/flashes.html.twig',
                ],
            ],

            'sylius_front.base.header.content' => [
                'logo' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/logo.html.twig',
                ],
                'security' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security.html.twig',
                ],
            ],

            'sylius_front.base.header.content.logo' => [
                'logo' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/logo.html.twig',
                ],
            ],
            'sylius_front.base.header.flashes' => [
                'logo' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/flashes.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.base.header.content.security' => [
                'logged_in_user' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user.html.twig',
                ],
                'visitor' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/visitor.html.twig',
                ],
            ],

            'sylius_front.base.header.content.security.logged_in_user' => [
                'mobile' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/mobile.html.twig',
                ],
                'desktop' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/desktop.html.twig',
                ],
            ],

            'sylius_front.base.header.content.security.logged_in_user.mobile' => [
                'menu' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/mobile/menu.html.twig',
                ],
            ],

            'sylius_front.base.header.content.security.logged_in_user.mobile.menu' => [
                'my_account' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/mobile/menu/my_account.html.twig',
                ],
                'logout' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/mobile/menu/logout.html.twig',
                ],
            ],

            'sylius_front.base.header.content.security.logged_in_user.desktop' => [
                'welcome_message' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/desktop/welcome_message.html.twig',
                ],
                'my_account' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/desktop/my_account.html.twig',
                ],
                'logout' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/logged_in_user/desktop/logout.html.twig',
                ],
            ],

            'sylius_front.base.header.content.security.visitor' => [
                'desktop' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/visitor/desktop.html.twig',
                ],
            ],

            'sylius_front.base.header.content.security.visitor.desktop' => [
                'user_icon' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/visitor/desktop/user_icon.html.twig',
                ],
                'login' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/visitor/desktop/login.html.twig',
                ],
                'register' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/layout/base/header/content/security/visitor/desktop/register.html.twig',
                ],
            ],
        ],
    ]);
};
