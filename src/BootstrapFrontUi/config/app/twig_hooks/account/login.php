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
            'sylius_front.account.login' => [
                'form' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form.html.twig',
                ],
            ],

            'sylius_front.account.login.form' => [
                'login_container' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container.html.twig',
                ],
                'register_container' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/register_container.html.twig',
                ],
            ],

            'sylius_front.account.login.form.login_container' => [
                'header' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container/header.html.twig',
                ],
                'errors' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container/errors.html.twig',
                ],
                'form_fields' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container/form_fields.html.twig',
                ],
                'submit' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container/submit.html.twig',
                ],
            ],

            'sylius_front.account.login.form.login_container.form_fields' => [
                'username' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container/form_fields/username.html.twig',
                ],
                'password' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container/form_fields/password.html.twig',
                ],
                'remember_me' => [
                    'template' => '@SyliusBootstrapFrontUi/account/login/form/login_container/form_fields/remember_me.html.twig',
                ],
            ],

            'sylius_front.account.login.form.register_container' => [
                'register_here' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/account/register_box.html.twig',
                ],
            ],
        ],
    ]);
};
