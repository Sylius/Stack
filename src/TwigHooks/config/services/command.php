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

use Sylius\TwigHooks\Console\Command\DebugTwigHooksCommand;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_twig_hooks.command.debug', DebugTwigHooksCommand::class)
        ->args([
            service('sylius_twig_hooks.registry.hookables'),
        ])
        ->tag('console.command')
    ;
};
