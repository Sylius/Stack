<?php

declare(strict_types=1);

namespace Sylius\TwigExtra\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class TwigExtraExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader(
            $container,
            new FileLocator(dirname(__DIR__, 3) . '/config'),
        );

        $loader->load('services.php');

        $configuration = $this->getConfiguration([], $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('sylius_twig_extra.twig_ux.anonymous_component_template_prefixes', $config['twig_ux']['anonymous_component_template_prefixes'] ?? []);
    }
}
