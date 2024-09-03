<?php

declare(strict_types=1);

namespace Tests\Sylius\TwigExtra\Functional\Twig\Extension;

use Sylius\TwigExtra\Twig\Extension\TestHtmlAttributeExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class TestHtmlAttributeExtensionTest extends KernelTestCase
{
    public function testTheContainerContainsTheService(): void
    {
        $this->bootKernel();

        $container = $this->getContainer();

        $this->assertTrue($container->has('sylius_twig_extra.twig.extension.test_html_attribute'));
        $this->assertInstanceOf(TestHtmlAttributeExtension::class, $container->get('sylius_twig_extra.twig.extension.test_html_attribute'));
    }
}
