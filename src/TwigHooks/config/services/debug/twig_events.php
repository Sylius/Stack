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

use Sylius\TwigHooks\Hook\Renderer\Debug\HookDebugCommentRenderer;
use Sylius\TwigHooks\Hook\Renderer\Debug\HookProfilerRenderer;
use Sylius\TwigHooks\Hookable\Renderer\Debug\HookableDebugCommentRenderer;
use Sylius\TwigHooks\Hookable\Renderer\Debug\HookableProfilerRenderer;
use Sylius\TwigHooks\Profiler\HooksDataCollector;
use Sylius\TwigHooks\Profiler\Profile;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return static function (ContainerBuilder $container, ContainerConfigurator $configurator): void {
    $env = $container->getParameter('kernel.environment');
    if ($env !== 'dev') {
        return;
    }

    $services = $configurator->services();

    $services->set('sylius_twig_hooks.renderer.hook.debug_comment', HookDebugCommentRenderer::class)
        ->decorate('sylius_twig_hooks.renderer.hook', priority: 256)
        ->args([
            service('.inner'),
        ])
    ;

    $services->set('sylius_twig_hooks.renderer.hook.profiler', HookProfilerRenderer::class)
        ->decorate('sylius_twig_hooks.renderer.hook', priority: 512)
        ->args([
            service('.inner'),
            service('sylius_twig_hooks.profiler.profile')->nullOnInvalid(),
            service('debug.stopwatch')->nullOnInvalid(),
        ])
    ;

    $services->set('sylius_twig_hooks.renderer.hookable.debug_comment', HookableDebugCommentRenderer::class)
        ->decorate('sylius_twig_hooks.renderer.hookable', priority: 256)
        ->args([
            service('.inner'),
        ])
    ;

    $services->set('sylius_twig_hooks.renderer.hookable.profiler', HookableProfilerRenderer::class)
        ->decorate('sylius_twig_hooks.renderer.hookable', priority: 512)
        ->args([
            service('.inner'),
            service('sylius_twig_hooks.profiler.profile')->nullOnInvalid(),
            service('debug.stopwatch')->nullOnInvalid(),
        ])
    ;

    $services->set('sylius_twig_hooks.profiler.profile', Profile::class);

    $services->set(HooksDataCollector::class)
        ->args([service('sylius_twig_hooks.profiler.profile')])
        ->tag('data_collector')
    ;

    $services->set(HooksDataCollector::class)
        ->args([service('sylius_twig_hooks.profiler.profile')])
        ->tag('data_collector', ['template' => '@SyliusTwigHooks/data_collector/hooks.html.twig', 'id' => 'sylius_twig_hooks'])
    ;
};
