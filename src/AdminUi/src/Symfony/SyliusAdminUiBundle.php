<?php

declare(strict_types=1);

namespace Sylius\AdminUi\Symfony;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SyliusAdminUiBundle extends AbstractBundle
{
    public function getPath(): string
    {
        if (!isset($this->path)) {
            $reflected = new \ReflectionObject($this);
            $this->path = \dirname($reflected->getFileName(), 3);
        }

        return $this->path;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../../config/services.php');

        $builder->setParameter('sylius_admin_ui.routing', $config['routing']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('routing')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

}
