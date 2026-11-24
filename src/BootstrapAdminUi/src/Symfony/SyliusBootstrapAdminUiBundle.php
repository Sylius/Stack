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

namespace Sylius\BootstrapAdminUi\Symfony;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SyliusBootstrapAdminUiBundle extends AbstractBundle
{
    public function getPath(): string
    {
        if (!isset($this->path)) {
            $reflected = new \ReflectionObject($this);
            $this->path = \dirname($reflected->getFileName(), 3);
        }

        return $this->path;
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('importmap_entrypoint')
                    ->info('Importmap entrypoint starting the Stimulus application of the admin panel, instead of the prebuilt Symfony UX assets. Requires symfony/asset-mapper.')
                    ->example('app')
                    ->defaultNull()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $bundles = $builder->getParameter('kernel.bundles');

        $loader = new PhpFileLoader(
            $builder,
            new FileLocator(dirname(__DIR__, 2) . '/config'),
        );

        $loader->load('services.php');

        if ($this->isAssetMapperAvailable($builder)) {
            // Exposes the Stimulus controllers to AssetMapper.
            $builder->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        $this->getPath() . '/assets/controllers' => '@sylius/bootstrap-admin-ui/controllers',
                    ],
                ],
            ]);

            // Registers the bundle controllers next to the application ones, in the Stimulus
            // application started by the application: "copy-to-clipboard", "theme-switcher".
            $builder->prependExtensionConfig('stimulus', [
                'controller_paths' => [
                    '%kernel.project_dir%/assets/controllers',
                    $this->getPath() . '/assets/controllers',
                ],
            ]);
        }

        if (!isset($bundles['SyliusAdminUiBundle'])) {
            return;
        }

        $container->extension('sylius_twig_hooks', [
            'enable_autoprefixing' => true,
            'hook_name_section_separator' => '#',
        ]);

        $entrypoint = $this->processConfiguration($builder)['importmap_entrypoint'];
        if (null === $entrypoint) {
            return;
        }

        if (!$this->isAssetMapperAvailable($builder)) {
            throw new \LogicException('The "sylius_bootstrap_admin_ui.importmap_entrypoint" option requires AssetMapper. Try running "composer require symfony/asset-mapper".');
        }

        // Only the configuration is prepended: the hookables themselves are defined in "config/app.php",
        // imported by the application, which would override an "enabled" key prepended here.
        $builder->prependExtensionConfig('sylius_twig_hooks', [
            'hooks' => [
                'sylius_admin.base#stylesheets' => [
                    'symfony_ux' => ['configuration' => ['entrypoint' => $entrypoint]],
                ],
                'sylius_admin.base#javascripts' => [
                    'symfony_ux' => ['configuration' => ['entrypoint' => $entrypoint]],
                ],
            ],
        ]);
    }

    /**
     * @return array{importmap_entrypoint: string|null}
     */
    private function processConfiguration(ContainerBuilder $builder): array
    {
        $extension = $this->getContainerExtension();
        \assert($extension instanceof ConfigurationExtensionInterface);
        $configs = $builder->getExtensionConfig($extension->getAlias());
        $configuration = $extension->getConfiguration($configs, $builder);
        \assert(null !== $configuration);

        /** @var array{importmap_entrypoint: string|null} $config */
        $config = (new Processor())->processConfiguration($configuration, $configs);

        return $config;
    }

    private function isAssetMapperAvailable(ContainerBuilder $builder): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        $bundlesMetadata = $builder->getParameter('kernel.bundles_metadata');

        return \is_array($bundlesMetadata) && isset($bundlesMetadata['FrameworkBundle']);
    }
}
