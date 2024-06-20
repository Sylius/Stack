<?php

declare(strict_types=1);

namespace Tests\Sylius\TwigExtra\Functional\Twig\Extension;

use Sylius\TwigExtra\Twig\TestHtmlAttributeExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class TestHtmlAttributeExtensionTest extends KernelTestCase
{
    public function testTheContainerContainsTheService(): void
    {
        $this->bootKernel();

        $container = $this->getContainer();

        $this->assertTrue($container->has('twig_extra.twig.extension.test_html_attribute'));
        $this->assertInstanceOf(TestHtmlAttributeExtension::class, $container->get('twig_extra.twig.extension.test_html_attribute'));
    }
}
