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

namespace Tests\Sylius\ShadcnAdminUi\Unit\Symfony;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\ShadcnAdminUi\Symfony\SyliusShadcnAdminUiBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\UX\StimulusBundle\StimulusBundle;

#[CoversClass(SyliusShadcnAdminUiBundle::class)]
final class SyliusShadcnAdminUiBundleTest extends TestCase
{
    public function testItRegistersTheControllersWhenStimulusBundleIsEnabled(): void
    {
        $builder = $this->prepend(['StimulusBundle' => StimulusBundle::class]);

        $this->assertNotSame([], $builder->getExtensionConfig('stimulus'));
    }

    public function testItDoesNotConfigureStimulusWhenStimulusBundleIsNotEnabled(): void
    {
        $builder = $this->prepend([]);

        $this->assertSame([], $builder->getExtensionConfig('stimulus'));
        $this->assertNotSame([], $builder->getExtensionConfig('framework'));
    }

    /**
     * @param array<string, class-string> $bundles
     */
    private function prepend(array $bundles): ContainerBuilder
    {
        $bundles = ['FrameworkBundle' => FrameworkBundle::class] + $bundles;

        $builder = new ContainerBuilder();
        $builder->setParameter('kernel.bundles', $bundles);
        $builder->setParameter('kernel.bundles_metadata', array_map(
            static fn (string $class): array => ['path' => \dirname((string) (new \ReflectionClass($class))->getFileName()), 'namespace' => ''],
            $bundles,
        ));
        $builder->setParameter('kernel.environment', 'test');
        $builder->setParameter('kernel.build_dir', sys_get_temp_dir());

        (new SyliusShadcnAdminUiBundle())->getContainerExtension()?->prepend($builder);

        return $builder;
    }
}
