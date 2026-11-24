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

namespace Tests\Sylius\BootstrapAdminUi\Unit\Symfony;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\AdminUi\Symfony\SyliusAdminUiBundle;
use Sylius\BootstrapAdminUi\Symfony\SyliusBootstrapAdminUiBundle;
use Sylius\TwigHooks\DependencyInjection\SyliusTwigHooksExtension;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(SyliusBootstrapAdminUiBundle::class)]
final class SyliusBootstrapAdminUiBundleTest extends TestCase
{
    public function testItKeepsThePrebuiltSymfonyUxAssetsByDefault(): void
    {
        $builder = $this->prepend([]);

        $this->assertSame([], $this->symfonyUxConfigurations($builder));
    }

    public function testItLoadsTheImportmapEntrypointInsteadOfThePrebuiltSymfonyUxAssets(): void
    {
        $builder = $this->prepend(['importmap_entrypoint' => 'app']);

        $this->assertSame([
            'sylius_admin.base#stylesheets' => ['entrypoint' => 'app'],
            'sylius_admin.base#javascripts' => ['entrypoint' => 'app'],
        ], $this->symfonyUxConfigurations($builder));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function prepend(array $config): ContainerBuilder
    {
        $bundles = [
            'FrameworkBundle' => FrameworkBundle::class,
            'SyliusAdminUiBundle' => SyliusAdminUiBundle::class,
        ];

        $builder = new ContainerBuilder();
        $builder->registerExtension(new SyliusTwigHooksExtension());
        $builder->setParameter('kernel.bundles', $bundles);
        $builder->setParameter('kernel.bundles_metadata', array_map(
            static fn (string $class): array => ['path' => \dirname((string) (new \ReflectionClass($class))->getFileName()), 'namespace' => ''],
            $bundles,
        ));
        $builder->setParameter('kernel.environment', 'test');
        $builder->setParameter('kernel.build_dir', sys_get_temp_dir());
        $builder->setParameter('kernel.debug', false);

        $bundle = new SyliusBootstrapAdminUiBundle();
        $extension = $bundle->getContainerExtension();
        $this->assertNotNull($extension);

        if ([] !== $config) {
            $builder->prependExtensionConfig($extension->getAlias(), $config);
        }

        $extension->prepend($builder);

        return $builder;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function symfonyUxConfigurations(ContainerBuilder $builder): array
    {
        $configurations = [];
        foreach ($builder->getExtensionConfig('sylius_twig_hooks') as $config) {
            foreach ($config['hooks'] ?? [] as $hookName => $hookables) {
                if (isset($hookables['symfony_ux']['configuration'])) {
                    $configurations[$hookName] = $hookables['symfony_ux']['configuration'];
                }
            }
        }

        return $configurations;
    }
}
