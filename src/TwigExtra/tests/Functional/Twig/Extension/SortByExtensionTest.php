<?php

declare(strict_types=1);

namespace Tests\Sylius\TwigExtra\Functional\Twig\Extension;

use Sylius\TwigExtra\Twig\SortByExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SortByExtensionTest extends KernelTestCase
{
    public function testTheContainerContainsTheService(): void
    {
        $this->bootKernel();

        $container = $this->getContainer();

        $this->assertTrue($container->has('twig_extra.twig.extension.sort_by'));
        $this->assertInstanceOf(SortByExtension::class, $container->get('twig_extra.twig.extension.sort_by'));
    }
}
