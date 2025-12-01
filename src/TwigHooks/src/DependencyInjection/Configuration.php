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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_twig_hooks');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enable_autoprefixing')->defaultFalse()->end()
                ->scalarNode('hook_name_section_separator')->defaultFalse()->end()
            ->end()
        ->end();

        $this->addSupportedHookableTypesConfiguration($rootNode);
        $this->addHooksConfiguration($rootNode);

        return $treeBuilder;
    }

    private function addSupportedHookableTypesConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('supported_hookable_types')
                    ->useAttributeAsKey('type')
                    ->defaultValue([
                        'template' => HookableTemplate::class,
                        'component' => HookableComponent::class,
                        'disabled' => DisabledHookable::class,
                    ])
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;
    }

    private function addHooksConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('hooks')
                    ->useAttributeAsKey('_name')
                    ->arrayPrototype()
                        ->useAttributeAsKey('_name')
                        ->arrayPrototype()
                            ->beforeNormalization()
                                ->always(function ($v) {
                                    $isTypeDefined = isset($v['type']);
                                    $isComponentDefined = isset($v['component']);
                                    $isTemplateDefined = isset($v['template']);
                                    $isDisabled = isset($v['enabled']) && $v['enabled'] === false;

                                    if (!$isComponentDefined && !$isTemplateDefined && !$isDisabled) {
                                        return $v;
                                    }

                                    if (true === $isTypeDefined && false === $isDisabled) {
                                        return $v;
                                    }

                                    $v['type'] = match (true) {
                                        $isDisabled => 'disabled',
                                        $isComponentDefined => 'component',
                                        $isTemplateDefined => 'template',
                                        default => 'undefined',
                                    };

                                    return $v;
                                })
                            ->end()
                            ->validate()
                                ->always(static function ($v) {
                                    $type = $v['type'] ?? null;
                                    if ('template' === $type) {
                                        $v['component'] = null;
                                        $v['props'] = [];
                                    }

                                    if ('component' === $type) {
                                        $v['template'] = null;
                                    }

                                    $component = $v['component'] ?? null;
                                    $template = $v['template'] ?? null;
                                    $enabled = $v['enabled'] ?? true;

                                    if (null !== $component && null !== $template) {
                                        throw new \InvalidArgumentException('You cannot define both "component" and "template" at the same time.');
                                    }

                                    if ($enabled && null === $component && null === $template) {
                                        throw new \InvalidArgumentException('You must define either "component" or "template" for enabled hookables.');
                                    }

                                    if (null === $component && [] !== $v['props']) {
                                        throw new \InvalidArgumentException('"Props" cannot be defined for non-component hookables.');
                                    }

                                    return $v;
                                })
                            ->end()
                            ->canBeDisabled()
                            ->children()
                                ->scalarNode('type')->defaultNull()->end()
                                ->scalarNode('component')->defaultNull()->end()
                                ->scalarNode('template')->defaultNull()->end()
                                ->arrayNode('context')
                                    ->defaultValue([])
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                                ->arrayNode('props')
                                    ->defaultValue([])
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                                ->arrayNode('configuration')
                                    ->defaultValue([])
                                    ->useAttributeAsKey('name')
                                    ->prototype('variable')->end()
                                ->end()
                                ->integerNode('priority')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
