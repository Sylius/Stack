<?php

declare(strict_types=1);

namespace Tests\Sylius\TwigExtra\Unit\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sylius\TwigExtra\Twig\Extension\TestFormAttributeExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Node\Node;

final class TestFormAttributeExtensionTest extends TestCase
{
    public function testItIsATwigExtension(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, new TestFormAttributeExtension('dev', false));
    }

    public function testItContainsATwigFunctionForFormAttributes(): void
    {
        $twigFunction = (new TestFormAttributeExtension('dev', false))->getFunctions()[0];

        $this->assertEquals('sylius_test_form_attribute', $twigFunction->getName());
    }

    public function testItsTwigFunctionAddsADataTestAttributeForTestEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => ['data-test-foo' => '']], ($callable)('foo'));
    }

    public function testItsTwigFunctionAddsADataTestAttributeWithValueForTestEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => ['data-test-foo' => 'fighters']], ($callable)('foo', 'fighters'));
    }

    public function testItsTwigFunctionDoesNothingForProdEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('prod', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals([], ($callable)('foo'));
    }

    public function testItsTwigFunctionAddsADataTestAttributeForProdEnvironmentIfDebugIsEnabled(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', true))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => ['data-test-foo' => '']], ($callable)('foo'));
    }

    public function testItsTwigFunctionIsSafeForHtml(): void
    {
        $twigFunction = (new TestFormAttributeExtension('dev', false))->getFunctions()[0];

        $this->assertEquals(['html'], $twigFunction->getSafe(new Node()));
    }
}
