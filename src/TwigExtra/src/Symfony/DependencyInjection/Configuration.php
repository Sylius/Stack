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

namespace Sylius\TwigExtra\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @phpstan-return TreeBuilder<'array'>
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_twig_extra');

        /**
         * @var ArrayNodeDefinition $rootNode
         * @phpstan-var ArrayNodeDefinition<NodeParentInterface|null> $rootNode
         */
        $rootNode = $treeBuilder->getRootNode();

        $this->addTwigUxConfiguration($rootNode);

        return $treeBuilder;
    }

    /**
     * @phpstan-param ArrayNodeDefinition<NodeParentInterface|null> $rootNode
     */
    private function addTwigUxConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('twig_ux')
                    ->children()
                        ->arrayNode('anonymous_component_template_prefixes')
                            ->useAttributeAsKey('prefix_name')
                                ->validate()
                                    ->always(static function ($values): array {
                                        foreach ($values as $path) {
                                            if (!is_string($path)) {
                                                throw new \InvalidArgumentException(sprintf('Path must be a string. "%s" given.', get_debug_type($path)));
                                            }
                                        }

                                        return $values;
                                    })
                                ->end()
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
