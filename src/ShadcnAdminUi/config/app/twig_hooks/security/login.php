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
            'sylius_admin.security.login' => [
                'page' => [
                    'template' => '@SyliusShadcnAdminUi/security/common/page.html.twig',
                ],
            ],

            'sylius_admin.security.login.page' => [
                'logo' => [
                    'template' => '@SyliusShadcnAdminUi/security/common/logo.html.twig',
                ],
                'content' => [
                    'template' => '@SyliusShadcnAdminUi/security/common/content.html.twig',
                ],
            ],

            'sylius_admin.security.login.page.logo' => [
                'image' => [
                    'template' => '@SyliusShadcnAdminUi/security/common/logo/image.html.twig',
                ],
            ],

            'sylius_admin.security.login.page.content' => [
                'header' => [
                    'template' => '@SyliusShadcnAdminUi/security/common/content/header.html.twig',
                ],
                'flashes' => [
                    'template' => '@SyliusShadcnAdminUi/shared/crud/common/content/flashes.html.twig',
                ],
                'form' => [
                    'template' => '@SyliusShadcnAdminUi/security/login/page/content/form.html.twig',
                ],
            ],

            'sylius_admin.security.login.page.content.form' => [
                'error' => [
                    'template' => '@SyliusShadcnAdminUi/security/login/page/content/form/error.html.twig',
                ],
                'username' => [
                    'template' => '@SyliusShadcnAdminUi/security/login/page/content/form/username.html.twig',
                ],
                'password' => [
                    'template' => '@SyliusShadcnAdminUi/security/login/page/content/form/password.html.twig',
                ],
                'remember_me' => [
                    'template' => '@SyliusShadcnAdminUi/security/login/page/content/form/remember_me.html.twig',
                ],
                'submit' => [
                    'template' => '@SyliusShadcnAdminUi/security/login/page/content/form/submit.html.twig',
                ],
            ],
        ],
    ]);
};
