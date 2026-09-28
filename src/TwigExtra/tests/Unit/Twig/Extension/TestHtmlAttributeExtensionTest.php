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
use Sylius\TwigExtra\Twig\Extension\TestHtmlAttributeExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Node\Node;

final class TestHtmlAttributeExtensionTest extends TestCase
{
    public function testItIsATwigExtension(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, new TestHtmlAttributeExtension('dev', false));
    }

    public function testItContainsATwigFunctionForHtmlAttributes(): void
    {
        $twigFunction = (new TestHtmlAttributeExtension('dev', false))->getFunctions()[0];

        $this->assertEquals('sylius_test_html_attribute', $twigFunction->getName());
    }

    public function testItsTwigFunctionAddsADataTestAttributeForTestEnvironment(): void
    {
        $twigFunction = (new TestHtmlAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals('data-test-foo=""', ($callable)('foo'));
    }

    public function testItsTwigFunctionAddsADataTestAttributeWithValueForTestEnvironment(): void
    {
        $twigFunction = (new TestHtmlAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals('data-test-foo="fighters"', ($callable)('foo', 'fighters'));
    }

    public function testItsTwigFunctionDoesNothingForProdEnvironment(): void
    {
        $twigFunction = (new TestHtmlAttributeExtension('prod', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals('', ($callable)('foo'));
    }

    public function testItsTwigFunctionAddsADataTestAttributeForProdEnvironmentIfDebugIsEnabled(): void
    {
        $twigFunction = (new TestHtmlAttributeExtension('prod', true))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);
        $this->assertEquals('data-test-foo=""', ($callable)('foo'));
    }

    public function testItsTwigFunctionIsSafeForHtml(): void
    {
        $twigFunction = (new TestHtmlAttributeExtension('dev', false))->getFunctions()[0];

        $this->assertEquals(['html'], $twigFunction->getSafe(new Node()));
    }

    public function testItsTwigFunctionEscapesHtmlSpecialChars(): void
    {
        $twigFunction = (new TestHtmlAttributeExtension('test', false))->getFunctions()[0];
        $callable = $twigFunction->getCallable();

        $this->assertIsCallable($callable);

        $input = '"><img src=x onerror=alert(1)';
        $expectedEscaped = 'data-test-comment="&quot;&gt;&lt;img src=x onerror=alert(1)"';

        $this->assertEquals($expectedEscaped, ($callable)('comment', $input));
    }
}
