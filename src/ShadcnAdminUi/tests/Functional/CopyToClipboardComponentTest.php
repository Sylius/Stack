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
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CopyToClipboardComponentTest extends KernelTestCase
{
    use RendersTwigTrait;

    private const CONTROLLER = '[data-controller~="admin--copy-to-clipboard"]';

    public function testItRendersWithTheDefaultConfiguration(): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:copy_to_clipboard value="customer@example.com" />');

        $controller = $crawler->filter(self::CONTROLLER);
        self::assertCount(1, $controller);
        self::assertSame('customer@example.com', $controller->attr('data-admin--copy-to-clipboard-value-value'));
        self::assertSame($this->trans('sylius.ui.copied_to_clipboard'), $controller->attr('data-admin--copy-to-clipboard-copied-label-value'));
        self::assertSame($this->trans('sylius.ui.unable_to_copy_to_clipboard'), $controller->attr('data-admin--copy-to-clipboard-error-label-value'));

        $button = $controller->filter('button[data-test-copy-to-clipboard]');
        self::assertCount(1, $button);
        self::assertSame('button', $button->attr('type'));
        self::assertSame('admin--copy-to-clipboard#copy', $button->attr('data-action'));
        self::assertSame($this->trans('sylius.ui.copy_to_clipboard'), $button->attr('aria-label'));
        self::assertSame($this->trans('sylius.ui.copy_to_clipboard'), $button->attr('title'));
        self::assertSame('button', $button->attr('data-admin--copy-to-clipboard-target'));
        self::assertNull($button->attr('data-bs-toggle'));
    }

    public function testItSafelyRendersAValueContainingSpecialCharacters(): void
    {
        $value = 'customer+"<script>"@example.com';

        $html = $this->render('<twig:sylius_shadcn_admin_ui:copy_to_clipboard :value="value" />', ['value' => $value]);
        $crawler = new Crawler($html);

        self::assertCount(0, $crawler->filter('script'));
        self::assertStringNotContainsString('<script>', $html);
        self::assertSame($value, $crawler->filter(self::CONTROLLER)->attr('data-admin--copy-to-clipboard-value-value'));
    }

    public function testItRendersTheDefaultClassesUnescaped(): void
    {
        $classes = $this->buttonClasses('<twig:sylius_shadcn_admin_ui:copy_to_clipboard value="x" />');

        self::assertContains("[&_svg:not([class*='size-'])]:size-3", $classes);
        self::assertContains('text-muted-foreground', $classes);
    }

    public function testClassIsAddedToTheDefaultClasses(): void
    {
        $template = '<twig:sylius_shadcn_admin_ui:copy_to_clipboard value="x" class="extra" data-test-copy-code />';
        $classes = $this->buttonClasses($template);

        self::assertSame('extra', end($classes));
        self::assertContains('text-muted-foreground', $classes);
        $crawler = $this->crawl($template);
        self::assertCount(1, $crawler->filter('button[data-test-copy-code]'));
        self::assertCount(0, $crawler->filter(self::CONTROLLER . '.extra'));
    }

    public function testButtonClassReplacesTheDefaultClasses(): void
    {
        self::assertSame(['my-button'], $this->buttonClasses('<twig:sylius_shadcn_admin_ui:copy_to_clipboard value="x" button_class="my-button" />'));
    }

    public function testItCanReplaceButtonClassesAndComposeActions(): void
    {
        $button = $this->crawl(<<<'TWIG'
            <twig:sylius_shadcn_admin_ui:copy_to_clipboard
                value="ABC"
                button_class="btn btn-outline-secondary"
                class="custom"
                data-action="click->analytics#track"
            />
            TWIG)->filter('button');

        self::assertSame('btn btn-outline-secondary custom', $button->attr('class'));
        self::assertSame('admin--copy-to-clipboard#copy click->analytics#track', $button->attr('data-action'));
    }

    public function testItAcceptsCustomLabels(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            <twig:sylius_shadcn_admin_ui:copy_to_clipboard
                value="product-code"
                copy_label="Copy product code"
                copied_label="Product code copied"
                error_label="Product code could not be copied"
            />
            TWIG);

        $controller = $crawler->filter(self::CONTROLLER);
        self::assertSame('Product code copied', $controller->attr('data-admin--copy-to-clipboard-copied-label-value'));
        self::assertSame('Product code could not be copied', $controller->attr('data-admin--copy-to-clipboard-error-label-value'));

        $button = $controller->filter('button');
        self::assertSame('Copy product code', $button->attr('aria-label'));
        self::assertSame('Copy product code', $button->attr('title'));
    }

    public function testItRendersAccessibleFeedbackAndIconTargets(): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:copy_to_clipboard value="product-code" />');

        self::assertCount(1, $crawler->filter('[data-admin--copy-to-clipboard-target~="copyIcon"]'));
        self::assertCount(1, $crawler->filter('[data-admin--copy-to-clipboard-target~="successIcon"]'));
        self::assertCount(1, $crawler->filter('[data-admin--copy-to-clipboard-target~="errorIcon"]'));
        self::assertCount(1, $crawler->filter('.sr-only[role="status"][aria-live="polite"][aria-atomic="true"][data-admin--copy-to-clipboard-target~="status"]'));
        self::assertCount(1, $crawler->filter(sprintf('button[aria-label="%s"]', $this->trans('sylius.ui.copy_to_clipboard'))));
        self::assertStringNotContainsString('Copy to clipboard', $crawler->html());
        self::assertStringNotContainsString('Copied to clipboard', $crawler->html());
        self::assertStringNotContainsString('Unable to copy to clipboard', $crawler->html());
    }

    public function testItRendersThroughTwigHooks(): void
    {
        $crawler = $this->crawl("{% hook 'app.copy_to_clipboard' with { value: 'ABC-123' } %}");

        self::assertCount(1, $crawler->filter('button[data-test-copy-to-clipboard]'));
        self::assertSame('ABC-123', $crawler->filter(self::CONTROLLER)->attr('data-admin--copy-to-clipboard-value-value'));
        self::assertCount(0, $crawler->filter('[hookableMetadata]'));
    }

    #[DataProvider('provideValues')]
    public function testItDisablesOnlyMissingOrEmptyValues(?string $value, bool $disabled): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:copy_to_clipboard :value="value" />', ['value' => $value]);

        self::assertCount($disabled ? 1 : 0, $crawler->filter('button[disabled]'));
        self::assertSame($value, $crawler->filter(self::CONTROLLER)->attr('data-admin--copy-to-clipboard-value-value'));
    }

    /**
     * @return iterable<string, array{?string, bool}>
     */
    public static function provideValues(): iterable
    {
        yield 'null' => [null, true];
        yield 'empty string' => ['', true];
        yield 'zero' => ['0', false];
        yield 'leading zeros' => ['000123', false];
        yield 'unicode and newlines' => ["Référence 日本語\nABC-123", false];
    }

    public function testItAcceptsAnIntegerZero(): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:copy_to_clipboard :value="0" />');

        self::assertCount(0, $crawler->filter('button[disabled]'));
        self::assertSame('0', $crawler->filter(self::CONTROLLER)->attr('data-admin--copy-to-clipboard-value-value'));
    }

    public function testItCanBeExplicitlyDisabled(): void
    {
        self::assertCount(1, $this->crawl('<twig:sylius_shadcn_admin_ui:copy_to_clipboard value="ABC" disabled />')->filter('button[disabled]'));
    }

    public function testItDisablesCopyingWhenNoValueIsProvided(): void
    {
        self::assertCount(1, $this->crawl('<twig:sylius_shadcn_admin_ui:copy_to_clipboard />')->filter('button[disabled]'));
    }

    public function testItCannotEnableCopyingAnAbsentValueWithTheDisabledProp(): void
    {
        self::assertCount(1, $this->crawl('<twig:sylius_shadcn_admin_ui:copy_to_clipboard :value="null" :disabled="false" />')->filter('button[disabled]'));
    }

    /**
     * @return list<string>
     */
    private function buttonClasses(string $template): array
    {
        preg_match('/<button\b[^>]*>/', $this->render($template), $button);
        self::assertSame(1, preg_match_all('/\sclass="/', $button[0] ?? ''), 'The button has a single class attribute.');

        $class = (string) $this->crawl($template)->filter('button')->attr('class');

        return array_values(array_filter(explode(' ', $class)));
    }

    private function trans(string $key): string
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);
        self::assertInstanceOf(TranslatorInterface::class, $translator);

        $label = $translator->trans($key);
        self::assertNotSame($key, $label, sprintf('The "%s" label is translated.', $key));

        return $label;
    }
}
