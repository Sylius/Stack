<?php

declare(strict_types=1);

namespace Sylius\TwigExtra\Symfony\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('sylius_twig_extra');

        $rootNode = $treeBuilder->getRootNode();

        $this->addTwigUxConfiguration($rootNode);

        return $treeBuilder;
    }

    private function addTwigUxConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('twig_ux')
                    ->children()
                        ->arrayNode('anonymous_component_template_prefixes')
                            ->useAttributeAsKey('prefix_name')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
