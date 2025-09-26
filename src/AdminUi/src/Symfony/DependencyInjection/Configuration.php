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

namespace Sylius\AdminUi\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_admin_ui');

        $rootNode = $treeBuilder->getRootNode();

        $this->addRoutingConfiguration($rootNode);

        return $treeBuilder;
    }

    private function addRoutingConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('routing')
                ->setDeprecated('sylius/admin-ui', '0.9', 'This configuration is not used anymore, use the Admin Ui routes instead.')
                    ->useAttributeAsKey('name')
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
        ;
    }
}
