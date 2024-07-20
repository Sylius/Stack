<?php

/*
 * This file is part of SyliusUi.
 *
 * (c) Monofony
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\BootstrapTheme\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SyliusBootstrapThemeBundle extends AbstractBundle
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
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $bundles = $builder->getParameter('kernel.bundles');

        if (!isset($bundles['SyliusAdminUiBundle'])) {
            return;
        }

        $container->extension('twig', [
            'globals' => [
                'sylius_admin_layout_base' => '@@SyliusBootstrapTheme/layout.html.twig',
            ],
        ]);

        $container->extension('twig_hooks', [
            'enable_autoprefixing' => true,
            'hook_name_section_separator' => '#'
        ]);

        $container->extension('twig_extra', [
            'twig_ux' => [
                'anonymous_component_template_prefixes' => [
                    'sylius_admin_ui' => '@SyliusBootstrapTheme/shared/components',
                ],
            ],
        ]);

        // $container->import('../../config/twig_hooks.php');
        // $container->import('../../config/sylius_grid.php');
    }
}
