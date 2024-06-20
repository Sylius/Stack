<?php

declare(strict_types=1);

namespace Tests\Sylius\TwigExtra\Functional\Twig\Extension;

use Sylius\TwigExtra\Twig\TestFormAttributeExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class TestFormAttributeExtensionTest extends KernelTestCase
{
    public function testTheContainerContainsTheService(): void
    {
        $this->bootKernel();

        $container = $this->getContainer();

        $this->assertTrue($container->has('twig_extra.twig.extension.test_form_attribute'));
        $this->assertInstanceOf(TestFormAttributeExtension::class, $container->get('twig_extra.twig.extension.test_form_attribute'));
    }
}
