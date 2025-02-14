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
            'sylius_admin.book.show.content' => [
                'page_body' => [
                    'template' => 'book/show/content/page_body.html.twig',
                ],
            ],

            'sylius_admin.book.show.content.header.title_block' => [
                'title' => [
                    'template' => '@SyliusBootstrapAdminUi/shared/crud/show/content/header/title_block/title.html.twig',
                    'configuration' => [
                        'title' => '@=_context.book.getTitle()',
                        'icon' => 'tabler:book',
                        'subheader' => '@=_context.book.getAuthorName()',
                    ],
                ],
            ],
        ],
    ]);
};
