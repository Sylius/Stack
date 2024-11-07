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

namespace Tests\Sylius\TwigHooks\Unit\Hookable\Renderer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\TwigHooks\Hookable\Metadata\HookableMetadata;
use Sylius\TwigHooks\Hookable\Renderer\Exception\HookRenderException;
use Sylius\TwigHooks\Hookable\Renderer\HookableTemplateRenderer;
use Tests\Sylius\TwigHooks\Utils\MotherObject\HookableComponentMotherObject;
use Tests\Sylius\TwigHooks\Utils\MotherObject\HookableTemplateMotherObject;
use Twig\Environment as Twig;
use Twig\Error\Error;

final class HookableTemplateRendererTest extends TestCase
{
    /** @var Twig&MockObject */
    private Twig $twig;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Twig::class);
    }

    public function testItSupportsOnlyHookableTemplates(): void
    {
        $hookableTemplate = HookableTemplateMotherObject::some();
        $hookableComponent = HookableComponentMotherObject::some();

        $this->assertTrue($this->getTestSubject()->supports($hookableTemplate));
        $this->assertFalse($this->getTestSubject()->supports($hookableComponent));
    }

    public function testItThrowsAnExceptionWhenTryingToRenderUnsupportedHookable(): void
    {
        $hookableComponent = HookableComponentMotherObject::some();
        $metadata = $this->createMock(HookableMetadata::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Hookable must be the "Sylius\TwigHooks\Hookable\HookableTemplate", but "Sylius\TwigHooks\Hookable\HookableComponent" given.');

        $this->getTestSubject()->render($hookableComponent, $metadata);
    }

    public function testItRendersHookableTemplate(): void
    {
        $metadata = $this->createMock(HookableMetadata::class);

        $this->twig->expects($this->once())->method('render')->with('some-template', [
            'hookable_metadata' => $metadata,
        ])->willReturn('some-rendered-template');

        $hookable = HookableTemplateMotherObject::withTarget('some-template');
        $renderedTemplate = $this->getTestSubject()->render($hookable, $metadata);

        $this->assertSame('some-rendered-template', $renderedTemplate);
    }

    public function testItThrowsAnExceptionWhenTwigThrowsAnError(): void
    {
        $metadata = $this->createMock(HookableMetadata::class);

        $this->twig->expects($this->once())->method('render')->with('some-template', [
            'hookable_metadata' => $metadata,
        ])->willThrowException(new Error('Unable to find the template.'));

        $hookable = HookableTemplateMotherObject::withTarget('some-template');

        $this->expectException(HookRenderException::class);
        $this->expectExceptionMessage('An error occurred during rendering the "some_name" hook in the "some_hook" hookable. Unable to find the template at line 76.');

        $this->getTestSubject()->render($hookable, $metadata);
    }

    private function getTestSubject(): HookableTemplateRenderer
    {
        return new HookableTemplateRenderer($this->twig);
    }
}
