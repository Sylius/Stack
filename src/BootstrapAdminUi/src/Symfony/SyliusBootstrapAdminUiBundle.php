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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
