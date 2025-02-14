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
            'sylius_admin.conference.create.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/create/content/header/title_block/title.html.twig',
                    'configuration' => [
                        'title' => 'app.ui.new_conference',
                        'icon' => 'tabler:plus',
                        'subheader' => 'app.ui.managing_your_conferences',
                    ],
                ],
            ],
        ],
    ]);
};
