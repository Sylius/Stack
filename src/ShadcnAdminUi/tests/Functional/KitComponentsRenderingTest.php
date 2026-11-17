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

namespace Tests\Sylius\ShadcnAdminUi\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Renders the components of the shadcn kit that the demo pages do not exercise.
 */
final class KitComponentsRenderingTest extends KernelTestCase
{
    use RendersTwigTrait;

    /**
     * @param list<string> $selectors
     */
    #[DataProvider('components')]
    public function testItRendersTheComponent(string $template, array $selectors): void
    {
        $crawler = $this->crawl($template);

        foreach ($selectors as $selector) {
            self::assertGreaterThan(0, $crawler->filter($selector)->count(), $selector);
        }
    }

    /**
     * @return iterable<string, array{string, list<string>}>
     */
    public static function components(): iterable
    {
        yield 'tooltip' => [
            <<<'TWIG'
                <twig:shadcn:Tooltip id="t">
                    <twig:shadcn:Tooltip:Trigger><twig:shadcn:Button {{ ...tooltip_trigger_attrs }}>B</twig:shadcn:Button></twig:shadcn:Tooltip:Trigger>
                    <twig:shadcn:Tooltip:Content>C</twig:shadcn:Tooltip:Content>
                </twig:shadcn:Tooltip>
                TWIG,
            ['[data-slot="tooltip"]', '[data-controller~="shadcn--tooltip"]', 'button[data-slot="tooltip-trigger"]', '[data-slot="tooltip-content"]'],
        ];

        yield 'dialog' => [
            <<<'TWIG'
                <twig:shadcn:Dialog id="d">
                    <twig:shadcn:Dialog:Trigger><twig:shadcn:Button {{ ...dialog_trigger_attrs }}>B</twig:shadcn:Button></twig:shadcn:Dialog:Trigger>
                    <twig:shadcn:Dialog:Content>C</twig:shadcn:Dialog:Content>
                </twig:shadcn:Dialog>
                TWIG,
            ['dialog[data-slot="dialog-content"]', 'button[data-slot="dialog-trigger"]', 'button[data-action*="shadcn--dialog#open"]'],
        ];

        yield 'alert dialog' => [
            <<<'TWIG'
                <twig:shadcn:AlertDialog id="a">
                    <twig:shadcn:AlertDialog:Trigger><twig:shadcn:Button {{ ...alert_dialog_trigger_attrs }}>B</twig:shadcn:Button></twig:shadcn:AlertDialog:Trigger>
                    <twig:shadcn:AlertDialog:Content>C</twig:shadcn:AlertDialog:Content>
                </twig:shadcn:AlertDialog>
                TWIG,
            ['dialog[data-slot="alert-dialog-content"]', 'button[data-action*="shadcn--alert-dialog#open"]'],
        ];

        yield 'sheet' => [
            <<<'TWIG'
                <twig:shadcn:Sheet id="s" side="left">
                    <twig:shadcn:Sheet:Trigger><twig:shadcn:Button {{ ...sheet_trigger_attrs }}>B</twig:shadcn:Button></twig:shadcn:Sheet:Trigger>
                    <twig:shadcn:Sheet:Content>C</twig:shadcn:Sheet:Content>
                </twig:shadcn:Sheet>
                TWIG,
            ['dialog[data-slot="sheet-content"]', 'button[data-slot="sheet-trigger"]'],
        ];
    }
}
