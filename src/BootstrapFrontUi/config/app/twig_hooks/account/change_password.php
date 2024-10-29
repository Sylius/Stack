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
            'sylius_front.account.change_password.update' => [
                'breadcrumbs' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/breadcrumbs.html.twig',
                    'priority' => 200,
                ],
                'menu' => [
                    'template' => '@SyliusBootstrapFrontUi/account/common/layout/menu.html.twig',
                    'priority' => 100,
                ],
                'content' => [
                    'template' => '@SyliusBootstrapFrontUi/account/common/layout/content.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.change_password.update.content' => [
                'form' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/form.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.change_password.update.content.header' => [
                'title' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/header/title.html.twig',
                    'priority' => 100,
                ],
                'subtitle' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/header/subtitle.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.change_password.update.content.form' => [
                'fields' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/form/fields.html.twig',
                    'priority' => 100,
                ],
                'buttons' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/form/buttons.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.change_password.update.content.form.fields' => [
                'current_password' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/form/fields/current_password.html.twig',
                    'priority' => 200,
                ],
                'new_password' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/form/fields/new_password.html.twig',
                    'priority' => 100,
                ],
                'confirm_new_password' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/form/fields/confirm_new_password.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.change_password.update.content.form.buttons' => [
                'submit' => [
                    'template' => '@SyliusBootstrapFrontUi/account/change_password/update/content/form/buttons/submit.html.twig',
                    'priority' => 0,
                ],
            ],
        ],
    ]);
};
