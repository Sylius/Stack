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
            'app.cms_page#navbar' => [
                'content' => [
                    'enabled' => false,
                ],
            ],
            'app.cms_page#content' => [
                'body' => [
                    'template' => 'cms/page/with_hooks/body.html.twig',
                ],
            ],
        ],
    ]);
};
