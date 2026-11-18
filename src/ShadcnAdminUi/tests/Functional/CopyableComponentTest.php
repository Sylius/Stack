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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CopyableComponentTest extends KernelTestCase
{
    use RendersTwigTrait;

    private const CONTAINER = 'body > span.inline-flex';

    private const CONTROLLER = '[data-controller~="admin--copy-to-clipboard"]';

    public function testItDisplaysAndCopiesTheValueByDefault(): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:copyable value="000123" />');

        self::assertSame('000123', trim($crawler->filter(self::CONTAINER)->text()));
        self::assertCount(1, $crawler->filter('button[data-test-copy-to-clipboard]'));
        self::assertSame('000123', $crawler->filter(self::CONTROLLER)->attr('data-admin--copy-to-clipboard-value-value'));
    }

    public function testItDisplaysAndCopiesAnIntegerZero(): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:copyable :value="0" />');

        self::assertSame('0', trim($crawler->filter(self::CONTAINER)->text()));
        self::assertCount(0, $crawler->filter('button[disabled]'));
        self::assertSame('0', $crawler->filter(self::CONTROLLER)->attr('data-admin--copy-to-clipboard-value-value'));
    }

    public function testItKeepsCustomContentSeparateFromTheCopyButtonAndValue(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            <twig:sylius_shadcn_admin_ui:copyable value="customer@example.com">
                <a href="mailto:customer@example.com"><span>Customer email</span></a>
            </twig:sylius_shadcn_admin_ui:copyable>
            TWIG);

        self::assertSame('Customer email', $crawler->filter('a')->text());
        self::assertSame('mailto:customer@example.com', $crawler->filter('a')->attr('href'));
        self::assertCount(0, $crawler->filter('a button, button a'));
        self::assertSame('customer@example.com', $crawler->filter(self::CONTROLLER)->attr('data-admin--copy-to-clipboard-value-value'));
    }

    public function testItPreservesDefaultButtonStylesWhenAddingClasses(): void
    {
        $template = '<twig:sylius_shadcn_admin_ui:copyable value="ABC" button:class="custom-button" />';
        preg_match('/<button\\b[^>]*>/', $this->render($template), $button);
        self::assertSame(1, preg_match_all('/\\sclass="/', $button[0] ?? ''), 'The button has a single class attribute.');

        $crawler = $this->crawl($template);
        $classes = explode(' ', (string) $crawler->filter('button')->attr('class'));
        self::assertContains('custom-button', $classes);
        self::assertContains('text-muted-foreground', $classes);
        self::assertCount(0, $crawler->filter('span.custom-button'));
    }

    public function testItSeparatesContainerAndButtonAttributes(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            <twig:sylius_shadcn_admin_ui:copyable
                value="ABC"
                class="custom-container"
                button:class="custom-button"
                button:data-test-copy-code
                button_class="btn btn-outline-secondary"
                copy_label="Copy code"
                copied_label="Code copied"
                error_label="Copy failed"
                disabled
            />
            TWIG);

        self::assertCount(1, $crawler->filter(self::CONTAINER . '.custom-container'));
        self::assertCount(0, $crawler->filter('button.custom-container'));
        $button = $crawler->filter('button.custom-button[data-test-copy-code][disabled]');
        self::assertCount(1, $button);
        self::assertSame('btn btn-outline-secondary custom-button', $button->attr('class'));
        self::assertSame('Copy code', $button->attr('aria-label'));
        $controller = $crawler->filter(self::CONTROLLER);
        self::assertSame('Code copied', $controller->attr('data-admin--copy-to-clipboard-copied-label-value'));
        self::assertSame('Copy failed', $controller->attr('data-admin--copy-to-clipboard-error-label-value'));
    }

    public function testItEscapesTheDisplayedValue(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% set value = '<script>alert("test")</script>' %}
            <twig:sylius_shadcn_admin_ui:copyable :value="value" />
            TWIG);

        self::assertCount(0, $crawler->filter('script'));
        self::assertSame('<script>alert("test")</script>', trim($crawler->filter(self::CONTAINER)->text()));
    }

    public function testItRendersThroughTwigHooks(): void
    {
        $crawler = $this->crawl("{% hook 'app.copyable' with { value: 'ABC-123' } %}");

        self::assertSame('ABC-123', trim($crawler->filter(self::CONTAINER)->text()));
        self::assertCount(1, $crawler->filter('button'));
        self::assertCount(0, $crawler->filter('[hookableMetadata]'));
    }

    public function testItDisablesCopyingAnAbsentValueButPreservesCustomContent(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            <twig:sylius_shadcn_admin_ui:copyable :value="null">
                <span>Not available</span>
            </twig:sylius_shadcn_admin_ui:copyable>
            TWIG);

        self::assertSame('Not available', trim($crawler->filter(self::CONTAINER)->text()));
        self::assertCount(1, $crawler->filter('button[disabled]'));
    }
}
