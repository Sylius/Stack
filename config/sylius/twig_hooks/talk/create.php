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

use App\Twig\Component\TalkFormComponent;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.talk.create.content' => [
                'form' => [
                    'component' => TalkFormComponent::class,
                    'props' => [
                        'form' => '@=_context.form',
                        'initialFormData' => '@=_context.resource',
                    ],
                ],
            ],
        ],
    ]);
};
