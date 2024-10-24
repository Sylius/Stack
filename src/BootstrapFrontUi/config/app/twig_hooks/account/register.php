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
            'sylius_front.account.register' => [
                'content' => [
                    'template' => '@SyliusBootstrapFrontUi/account/register/content.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.register.content' => [
                'header' => [
                    'template' => '@SyliusBootstrapFrontUi/account/register/content/header.html.twig',
                    'priority' => 100,
                ],
                'not_implemented_form' => [
                    'template' => '@SyliusBootstrapFrontUi/shared/form/not_implemented_form.html.twig',
                    'priority' => 0,
                ],
            ],

            'sylius_front.account.register.content.header' => [
                'title' => [
                    'template' => '@SyliusBootstrapFrontUi/account/register/content/header/title.html.twig',
                    'priority' => 100,
                ],
                'subtitle' => [
                    'template' => '@SyliusBootstrapFrontUi/account/register/content/header/subtitle.html.twig',
                    'priority' => 0,
                ],
            ],
        ],
    ]);
};
