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

namespace Sylius\ShadcnAdminUi\Symfony;

use Sylius\ShadcnAdminUi\Twig\Extension\UxTwoCompatibilityExtension;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\UX\TwigComponent\MountedComponent;

/**
 * Tailwind CSS + shadcn/ui flavour of the Sylius Stack admin panel.
 *
 * It fills the "empty" templates of sylius/admin-ui through Twig Hooks, exactly like
 * sylius/bootstrap-admin-ui does, and keeps the same hook and hookable names so that
 * existing customisations keep working after switching themes.
 */
final class SyliusShadcnAdminUiBundle extends AbstractBundle
{
    /** Page hooks of sylius/admin-ui that render the "sidebar" hookable. */
    private const SIDEBAR_HOOKS = [
        'sylius_admin.common.index',
        'sylius_admin.common.show',
        'sylius_admin.common.create',
        'sylius_admin.common.update',
        'sylius_admin.dashboard.index',
    ];

    public function getPath(): string
    {
        return \dirname(__DIR__, 2);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('sidebar')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->enumNode('variant')
                            ->info('Default variant of the sidebar on every admin page, overridable per page hook.')
                            ->values(['sidebar', 'floating', 'inset'])
                            ->defaultValue('sidebar')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $bundles = $builder->getParameter('kernel.bundles');

        // Hooks, grid templates and anonymous component prefixes live in "config/app.php",
        // imported by the application (see README), exactly like sylius/bootstrap-admin-ui.
        // Importing them here would append them after the application configuration
        // and silently override its hooks.

        if (isset($bundles['SyliusAdminUiBundle'])) {
            $builder->prependExtensionConfig('sylius_twig_hooks', [
                'enable_autoprefixing' => true,
                'hook_name_section_separator' => '#',
            ]);

            // Prepended, so a "sidebar" hookable configured on one page hook by the application still wins.
            $variant = $this->processConfiguration($builder)['sidebar']['variant'];
            $builder->prependExtensionConfig('sylius_twig_hooks', [
                'hooks' => array_fill_keys(self::SIDEBAR_HOOKS, [
                    'sidebar' => ['configuration' => ['variant' => $variant]],
                ]),
            ]);
        }

        if ($this->isAssetMapperAvailable($builder)) {
            // Exposes the theme stylesheet and the Stimulus controllers to AssetMapper.
            $builder->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        $this->getPath() . '/assets' => '@sylius/shadcn-admin-ui',
                    ],
                ],
            ]);

            // Registers the bundle controllers next to the application ones.
            // Their identifiers come from their sub-directory: "shadcn--dialog", "admin--grid-selection"...
            $builder->prependExtensionConfig('stimulus', [
                'controller_paths' => [
                    '%kernel.project_dir%/assets/controllers',
                    $this->getPath() . '/assets/controllers',
                ],
            ]);
        }
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../../config/services.php');

        if (!method_exists(MountedComponent::class, 'provide')) {
            $container->services()
                ->set('sylius_shadcn_admin_ui.twig.extension.ux_two_compatibility', UxTwoCompatibilityExtension::class)
                ->args([service('ux.twig_component.component_stack'), service('twig.runtime.tailwind')])
                // Registered last, for its "tailwind_classes" filter to win over the original one.
                ->tag('twig.extension', ['priority' => -1000])
            ;
        }
    }

    /**
     * @return array{sidebar: array{variant: string}}
     */
    private function processConfiguration(ContainerBuilder $builder): array
    {
        $extension = $this->getContainerExtension();
        $configs = $builder->getExtensionConfig($extension->getAlias());

        return (new Processor())->processConfiguration($extension->getConfiguration($configs, $builder), $configs);
    }

    private function isAssetMapperAvailable(ContainerBuilder $builder): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        $bundlesMetadata = $builder->getParameter('kernel.bundles_metadata');

        return isset($bundlesMetadata['FrameworkBundle']);
    }
}
