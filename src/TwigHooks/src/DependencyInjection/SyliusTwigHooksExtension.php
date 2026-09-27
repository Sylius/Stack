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

namespace Sylius\TwigHooks\DependencyInjection;

use Sylius\TwigHooks\Hookable\DisabledHookable;
use Sylius\TwigHooks\Hookable\HookableComponent;
use Sylius\TwigHooks\Hookable\HookableTemplate;
use Sylius\TwigHooks\Provider\Exception\InvalidExpressionException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Parser;

final class SyliusTwigHooksExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(dirname(__DIR__, 2) . '/config'));
        $loader->load('services.php');

        if ($container->hasParameter('kernel.debug') && $container->getParameter('kernel.debug')) {
            $loader->load('services/debug/twig_events.php');
        }

        $configuration = $this->getConfiguration([], $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerHooks($container, $config['hooks'], $config['supported_hookable_types']);
        $container->setParameter('sylius_twig_hooks.enable_autoprefixing', $config['enable_autoprefixing']);
        $container->setParameter('sylius_twig_hooks.hook_name_section_separator', $config['hook_name_section_separator']);
    }

    /**
     * @param array<string, mixed> $hooks
     * @param array<string, string> $supportedHookableTypes
     */
    private function registerHooks(ContainerBuilder $container, array $hooks, array $supportedHookableTypes): void
    {
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->register('is_granted', static fn (string ...$arguments): string => sprintf('is_granted(%s)', implode(', ', $arguments)), static fn (): bool => false);

        foreach ($hooks as $hookName => $hookables) {
            foreach ($hookables as $hookableName => $hookable) {
                if (!array_key_exists($hookable['type'], $supportedHookableTypes)) {
                    throw new \InvalidArgumentException(sprintf('Hookable type "%s" is not supported.', $hookable['type']));
                }

                $this->lintCondition($expressionLanguage, $hookName, $hookableName, $hookable);

                $this->registerHookable(
                    $container,
                    $supportedHookableTypes[$hookable['type']],
                    $hookName,
                    $hookableName,
                    $hookable,
                );
            }
        }
    }

    /**
     * @param array<string, mixed> $hookable
     */
    private function lintCondition(ExpressionLanguage $expressionLanguage, string $hookName, string $hookableName, array $hookable): void
    {
        $condition = $hookable['condition'] ?? null;

        if (null === $condition || !defined(Parser::class . '::IGNORE_UNKNOWN_VARIABLES')) {
            return;
        }

        try {
            $expressionLanguage->lint(substr($condition, 2), [], Parser::IGNORE_UNKNOWN_VARIABLES);
        } catch (\Throwable $e) {
            throw new InvalidExpressionException(
                sprintf(
                    'Failed to lint the "%s" condition of the "%s" hookable in the "%s" hook. Error: %s".',
                    $condition,
                    $hookableName,
                    $hookName,
                    $e->getMessage(),
                ),
                previous: $e,
            );
        }
    }

    /**
     * @param array<string, mixed> $hookable
     */
    private function registerHookable(
        ContainerBuilder $container,
        string $class,
        string $hookName,
        string $hookableName,
        array $hookable,
    ): void {
        match ($class) {
            HookableTemplate::class => $this->registerTemplateHookable($container, $hookName, $hookableName, $hookable),
            HookableComponent::class => $this->registerComponentHookable($container, $hookName, $hookableName, $hookable),
            DisabledHookable::class => $this->registerDisabledHookable($container, $hookName, $hookableName),
            default => throw new \InvalidArgumentException(sprintf('Unsupported hookable class "%s".', $class)),
        };
    }

    /**
     * @param array<string, mixed> $hookable
     */
    private function registerTemplateHookable(
        ContainerBuilder $container,
        string $hookName,
        string $hookableName,
        array $hookable,
    ): void {
        $container
            ->register(sprintf('sylius_twig_hooks.hook.%s.hookable.%s', $hookName, $hookableName), HookableTemplate::class)
            ->setArguments([
                $hookName,
                $hookableName,
                $hookable['template'],
                $hookable['context'],
                $hookable['configuration'],
                $hookable['priority'],
                $hookable['condition'],
            ])
            ->addTag('sylius_twig_hooks.hookable', ['priority' => $hookable['priority']])
        ;
    }

    /**
     * @param array<string, mixed> $hookable
     */
    private function registerComponentHookable(
        ContainerBuilder $container,
        string $hookName,
        string $hookableName,
        array $hookable,
    ): void {
        $container
            ->register(sprintf('sylius_twig_hooks.hook.%s.hookable.%s', $hookName, $hookableName), HookableComponent::class)
            ->setArguments([
                $hookName,
                $hookableName,
                $hookable['component'],
                $hookable['props'] ?? [],
                $hookable['context'],
                $hookable['configuration'],
                $hookable['priority'],
                $hookable['condition'],
            ])
            ->addTag('sylius_twig_hooks.hookable', ['priority' => $hookable['priority']])
        ;
    }

    private function registerDisabledHookable(
        ContainerBuilder $container,
        string $hookName,
        string $hookableName,
    ): void {
        $container
            ->register(sprintf('sylius_twig_hooks.hook.%s.hookable.%s', $hookName, $hookableName), DisabledHookable::class)
            ->setArguments([
                $hookName,
                $hookableName,
                [],
                [],
                null,
            ])
            ->addTag('sylius_twig_hooks.hookable', ['priority' => 0])
        ;
    }
}
