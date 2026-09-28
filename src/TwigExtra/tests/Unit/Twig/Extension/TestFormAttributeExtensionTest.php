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

    public function testItContainsATwigFunctionForFormAttribute(): void
    {
        $twigFunction = (new TestFormAttributeExtension('dev', false))->getFunctions()[0];

        $this->assertEquals('sylius_test_form_attribute', $twigFunction->getName());
    }

    public function testItContainsATwigFunctionForFormAttributes(): void
    {
        $twigFunction = (new TestFormAttributeExtension('dev', false))->getFunctions()[1];

        $this->assertEquals('sylius_test_form_attributes', $twigFunction->getName());
    }

    public function testItsTestFormAttributeTwigFunctionAddsADataTestAttributeForTestEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => ['data-test-foo' => '']], ($callable)('foo'));
    }

    public function testItsTestFormAttributesTwigFunctionAddsDataTestAttributesForTestEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', false))->getFunctions()[1];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => [
            'data-test-foo' => 'bar',
            'data-test-title' => 'Lord Of The Rings',
        ]], ($callable)([
            'foo' => 'bar',
            'title' => 'Lord Of The Rings',
        ]));
    }

    public function testItsTestFormAttributeTwigFunctionAddsADataTestAttributeWithValueForTestEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => ['data-test-foo' => 'fighters']], ($callable)('foo', 'fighters'));
    }

    public function testItsTestFormAttributesTwigFunctionAddsDataTestAttributesWithValueForTestEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', false))->getFunctions()[1];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => [
            'data-test-foo' => 'bar',
            'data-test-title' => 'Lord Of The Rings',
        ]], ($callable)([
            'foo' => 'bar',
            'title' => 'Lord Of The Rings',
        ]));
    }

    public function testItsTestFormAttributeTwigFunctionDoesNothingForProdEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('prod', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals([], ($callable)('foo'));
    }

    public function testItsTestFormAttributesTwigFunctionDoesNothingForProdEnvironment(): void
    {
        $twigFunction = (new TestFormAttributeExtension('prod', false))->getFunctions()[1];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals([], ($callable)(['foo' => 'bar']));
    }

    public function testItsTwigFunctionAddsADataTestAttributeForProdEnvironmentIfDebugIsEnabled(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', true))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => ['data-test-foo' => '']], ($callable)('foo'));
    }

    public function testItsTwigFunctionAddsADataTestAttributesForProdEnvironmentIfDebugIsEnabled(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', true))->getFunctions()[1];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals(['attr' => [
            'data-test-foo' => 'bar',
            'data-test-title' => 'Lord Of The Rings',
        ]], ($callable)([
            'foo' => 'bar',
            'title' => 'Lord Of The Rings',
        ]));
    }

    public function testItsTestFormAttributeTwigFunctionIsSafeForHtml(): void
    {
        $twigFunction = (new TestFormAttributeExtension('dev', false))->getFunctions()[0];

        $this->assertEquals(['html'], $twigFunction->getSafe(new Node()));
    }

    public function testItsTestFormAttributesTwigFunctionIsSafeForHtml(): void
    {
        $twigFunction = (new TestFormAttributeExtension('dev', false))->getFunctions()[1];

        $this->assertEquals(['html'], $twigFunction->getSafe(new Node()));
    }

    public function testItsTestFormAttributeEscapesHtmlSpecialCharacters(): void
    {
        $twigFunction = (new TestFormAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);

        $input = '\'" onmouseover="alert(1)';
        $expected = ['attr' => ['data-test-comment' => '&#039;&quot; onmouseover=&quot;alert(1)']];

        $this->assertEquals($expected, ($callable)('comment', $input));
    }
}
