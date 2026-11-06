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
use Symfony\Component\Finder\Finder;

/**
 * Same assertions under Symfony UX 2 (compatibility extension) and Symfony UX 3 (native features).
 */
final class UxCompatibilityTest extends KernelTestCase
{
    use RendersTwigTrait;

    private const DIALOG = '<twig:shadcn:Dialog id="%1$s"><twig:shadcn:Dialog:Content><twig:shadcn:Dialog:Title>T</twig:shadcn:Dialog:Title></twig:shadcn:Dialog:Content></twig:shadcn:Dialog>';

    public function testNestedInjectReadsTheProvidedValue(): void
    {
        $crawler = $this->crawl(sprintf(self::DIALOG, 'd'));

        self::assertSame('dialog-d-title', $crawler->filter('dialog')->attr('aria-labelledby'));
        self::assertSame('dialog-d-title', $crawler->filter('[data-slot="dialog-title"]')->attr('id'));
    }

    public function testSiblingComponentsKeepTheirOwnValues(): void
    {
        $crawler = $this->crawl(sprintf(self::DIALOG, 'a') . sprintf(self::DIALOG, 'b'));

        self::assertSame('dialog-a-title', $crawler->filter('dialog')->eq(0)->attr('aria-labelledby'));
        self::assertSame('dialog-b-title', $crawler->filter('dialog')->eq(1)->attr('aria-labelledby'));
        self::assertSame('dialog-a-title', $crawler->filter('[data-slot="dialog-title"]')->eq(0)->attr('id'));
        self::assertSame('dialog-b-title', $crawler->filter('[data-slot="dialog-title"]')->eq(1)->attr('id'));
    }

    public function testCallerClassWinsOverConflictingDefaults(): void
    {
        $classes = $this->classesOfCardTitle('<twig:shadcn:Card:Title class="text-3xl">T</twig:shadcn:Card:Title>');

        self::assertContains('text-3xl', $classes);
        self::assertContains('cn-font-heading', $classes);
        self::assertNotContains('text-base', $classes);
    }

    public function testStringableCallerClassIsResolved(): void
    {
        $classes = $this->classesOfCardTitle('<twig:Fixture:ForwardingCard />');

        self::assertContains('text-3xl', $classes);
        self::assertNotContains('text-base', $classes);
    }

    public function testNoCallerClassKeepsTheDefaults(): void
    {
        $classes = $this->classesOfCardTitle('<twig:shadcn:Card:Title>T</twig:shadcn:Card:Title>');

        self::assertContains('text-base', $classes);
        self::assertContains('leading-snug', $classes);
    }

    public function testArrayInputIgnoresEmptyValues(): void
    {
        $crawler = $this->crawl('<twig:Fixture:ArrayClasses class="font-bold" />');

        $classes = explode(' ', (string) $crawler->filter('[data-test="array-classes"]')->attr('class'));
        sort($classes);

        self::assertSame(['font-bold', 'text-base'], array_values(array_filter($classes)));
    }

    public function testDataActionIsMerged(): void
    {
        $crawler = $this->crawl('<twig:shadcn:Tooltip id="t"><twig:shadcn:Tooltip:Trigger><twig:Fixture:ActionButton {{ ...tooltip_trigger_attrs }} /></twig:shadcn:Tooltip:Trigger></twig:shadcn:Tooltip>');

        $action = (string) $crawler->filter('button')->attr('data-action');

        self::assertStringContainsString('mouseenter->shadcn--tooltip#show', $action);
        self::assertStringContainsString('click->fixture#default', $action);
    }

    public function testInjectOutsideAComponentReturnsTheDefault(): void
    {
        self::assertSame('fallback', $this->render("{{ inject('nope', 'fallback') }}"));
    }

    public function testProvideOutsideAComponentThrows(): void
    {
        $this->expectExceptionMessage('cannot be called outside of a component template');

        $this->render("{% do provide('key', 1) %}");
    }

    public function testKitUsesTailwindClassesOnlyThroughDefaults(): void
    {
        $files = (new Finder())->files()->in(\dirname(__DIR__, 2) . '/templates/components/shadcn')->name('*.html.twig');

        self::assertGreaterThan(0, $files->count());

        foreach ($files as $file) {
            self::assertDoesNotMatchRegularExpression('/class="[^"]*tailwind_classes/', $file->getContents(), $file->getRelativePathname());
        }
    }

    /**
     * @return list<string>
     */
    private function classesOfCardTitle(string $template): array
    {
        return explode(' ', (string) $this->crawl($template)->filter('[data-slot="card-title"]')->attr('class'));
    }
}
