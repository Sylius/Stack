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

final class DropdownMacroTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testListRendersTheItemsWithTheirClass(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.list({id: 'user', button: {text: 'Menu'}}, [
                {type: 'header', title: 'Account'},
                {title: 'Profile', url: '/profile', class: 'my-item'},
                {type: 'divider'},
                {title: 'Logout', url: '/logout'},
            ]) }}
            TWIG);

        self::assertSame('dropdown-menu-user-content', $crawler->filter('#dropdown-menu-user-trigger')->attr('aria-controls'));
        self::assertSame('Account', trim($crawler->filter('[data-slot="dropdown-menu-label"]')->text()));
        self::assertCount(1, $crawler->filter('[data-slot="dropdown-menu-separator"]'));

        $profile = $crawler->filter('a[role="menuitem"][href="/profile"]');
        self::assertStringContainsString('my-item', (string) $profile->attr('class'));
        self::assertSame('Profile', $profile->attr('data-test-user-dropdown-item'));
        self::assertCount(2, $crawler->filter('[data-test-user-dropdown-item]'));
    }

    public function testTheButtonTriggerOpensTheMenuAndKeepsItsClass(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.default({id: 'limit', button: {text: 'Show 10', class: 'h-7'}, content: ''}) }}
            TWIG);

        $trigger = $crawler->filter('button#dropdown-menu-limit-trigger');
        self::assertSame('trigger', $trigger->attr('data-shadcn--dropdown-menu-target'));
        self::assertStringContainsString('shadcn--dropdown-menu#toggle', (string) $trigger->attr('data-action'));
        $classes = explode(' ', (string) $trigger->attr('class'));
        self::assertContains('h-7', $classes);
        self::assertNotContains('h-8', $classes);
    }

    public function testTheButtonTriggerTakesTheButtonMacroOptions(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.default({id: 'opts', button: {text: 'Actions', icon_only: true, icon: 'lucide:ellipsis', size: 'small', variant: 'ghost', disabled: true, attr: {'data-test': 'opts', 'data-action': 'click->other#run'}}, content: ''}) }}
            TWIG);

        $trigger = $crawler->filter('button#dropdown-menu-opts-trigger');
        self::assertCount(1, $trigger);
        self::assertNotNull($trigger->attr('disabled'));
        self::assertSame('Actions', $trigger->attr('title'));
        self::assertSame('Actions', trim($trigger->filter('.sr-only')->text()));
        self::assertSame('opts', $trigger->attr('data-test'));
        self::assertSame('menu', $trigger->attr('aria-haspopup'));
        // The trigger action is rendered first, so the browser keeps it over the duplicate from attr.
        self::assertStringContainsString('shadcn--dropdown-menu#toggle', (string) $trigger->attr('data-action'));
        $classes = explode(' ', (string) $trigger->attr('class'));
        self::assertContains('size-7', $classes);
        self::assertNotContains('bg-background', $classes);
        // icon_only: no chevron, the button stays square.
        self::assertCount(1, $trigger->filter('svg'));
    }

    public function testTheButtonTriggerShowsAChevron(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.default({id: 'chevron', button: {text: 'Menu'}, content: ''}) }}
            TWIG);

        self::assertCount(1, $crawler->filter('#dropdown-menu-chevron-trigger svg[data-icon="inline-end"]'));
    }

    public function testAListItemWithoutUrlIsAButton(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.list({id: 'list'}, [{title: 'Refresh'}]) }}
            TWIG);

        self::assertCount(0, $crawler->filter('a[role="menuitem"]'));
        self::assertNull($crawler->filter('button[role="menuitem"]')->attr('href'));
    }

    public function testItemRendersALinkOnlyWithAnHref(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.item({href: '/books'}, 'Books') }}
            {{ dropdown.item({href: ''}, 'Empty') }}
            {{ dropdown.item({href: null}, 'Null') }}
            TWIG);

        self::assertSame(['/books'], $crawler->filter('a[role="menuitem"]')->each(static fn ($link): string => (string) $link->attr('href')));
        self::assertCount(2, $crawler->filter('button[role="menuitem"]'));
        self::assertCount(0, $crawler->filter('button[href]'));
    }

    public function testItemRendersAButtonWithItsVariant(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.default({id: 'row', customTrigger: 'More', content: dropdown.item({}, 'Delete', 'destructive'), direction: 'down-end'}) }}
            TWIG);

        self::assertSame('end', $crawler->filter('[data-slot="dropdown-menu-content"]')->attr('data-align'));
        $trigger = $crawler->filter('#dropdown-menu-row-trigger');
        self::assertSame('button', $trigger->attr('role'));
        self::assertSame('dropdown-menu-row-content', $trigger->attr('aria-controls'));
        self::assertSame('trigger', $trigger->attr('data-shadcn--dropdown-menu-target'));
        self::assertStringContainsString('shadcn--dropdown-menu#toggle', (string) $trigger->attr('data-action'));
        $item = $crawler->filter('button[role="menuitem"]');
        self::assertSame('destructive', $item->attr('data-variant'));
        self::assertSame('Delete', trim($item->text()));
    }

    public function testTheCustomTriggerOpensTheMenuWithEnterAndSpace(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/dropdown.html.twig' as dropdown %}
            {{ dropdown.default({id: 'row', customTrigger: 'More'}) }}
            TWIG);

        $actions = explode(' ', (string) $crawler->filter('#dropdown-menu-row-trigger')->attr('data-action'));
        self::assertContains('click->shadcn--dropdown-menu#toggle', $actions);
        self::assertContains('keydown.enter->shadcn--dropdown-menu#toggle', $actions);
        self::assertContains('keydown.space->shadcn--dropdown-menu#onTriggerKeydown:prevent', $actions);
        self::assertContains('keyup.space->shadcn--dropdown-menu#toggle', $actions);
    }
}
