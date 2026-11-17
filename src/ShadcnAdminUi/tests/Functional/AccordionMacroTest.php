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

final class AccordionMacroTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testItLinksEachHeadingTriggerToItsPanel(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/accordion.html.twig' as accordion %}
            {{ accordion.accordion([
                {title: 'First <b>1</b>', content: 'A'},
                {title: 'Second', content: 'B', open: true, id: 'second'},
            ]) }}
            TWIG);

        $triggers = $crawler->filter('button[data-shadcn--collapsible-target="trigger"]');
        self::assertSame(['false', 'true'], $triggers->each(static fn ($trigger): string => (string) $trigger->attr('aria-expanded')));
        self::assertStringContainsString('First <b>1</b>', $triggers->eq(0)->text());

        self::assertSame('second', $triggers->eq(1)->attr('aria-controls'));
        self::assertSame('collapsible-content', $crawler->filter('#second')->attr('data-slot'));
        self::assertSame('collapsible-content', $crawler->filter('#' . $triggers->eq(0)->attr('aria-controls'))->attr('data-slot'));
        self::assertCount(2, $crawler->filter('h2 > button[data-shadcn--collapsible-target="trigger"]'));
    }

    public function testItWiresTheSingleOpenControllerByDefault(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/accordion.html.twig' as accordion %}
            {{ accordion.accordion([{title: 'A', content: 'A'}]) }}
            TWIG);

        $card = $crawler->filter('[data-slot="card"]');
        self::assertSame('admin--accordion', $card->attr('data-controller'));
        self::assertSame('click->shadcn--collapsible#toggle admin--accordion#closeOthers', $crawler->filter('button[data-shadcn--collapsible-target="trigger"]')->attr('data-action'));
    }

    public function testEachChevronFollowsItsOwnTrigger(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/accordion.html.twig' as accordion %}
            {{ accordion.accordion([{title: 'Outer', open: true, content: accordion.accordion([{title: 'Inner', content: 'A'}])}]) }}
            TWIG);

        $triggers = $crawler->filter('button.group\/trigger');
        self::assertSame(['open', 'closed'], $triggers->each(static fn ($trigger): string => (string) $trigger->attr('data-state')));
        self::assertCount(0, $crawler->filter('.group\/collapsible'));
        $triggers->each(static fn ($trigger) => self::assertStringContainsString('group-data-[state=open]/trigger:rotate-180', (string) $trigger->filter('svg')->last()->attr('class')));
    }

    public function testAutoCloseFalseAllowsSeveralOpenItems(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/accordion.html.twig' as accordion %}
            {{ accordion.accordion([{title: 'A', content: 'A'}, {title: 'B', content: 'B'}], true, false) }}
            TWIG);

        self::assertNull($crawler->filter('[data-slot="card"]')->attr('data-controller'));
        self::assertCount(2, $crawler->filter('button[aria-expanded="true"]'));
    }
}
