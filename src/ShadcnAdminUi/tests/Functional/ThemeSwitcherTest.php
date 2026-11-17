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

final class ThemeSwitcherTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testItRendersADropdownWithTheLightDarkAndAutoModes(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% include '@SyliusShadcnAdminUi/shared/crud/common/navbar/items/theme_switcher.html.twig' %}
            TWIG);

        $menu = $crawler->filter('[data-slot="dropdown-menu"]');
        self::assertSame('shadcn--dropdown-menu admin--theme-switcher', $menu->attr('data-controller'));
        self::assertSame('dropdown-menu-admin-theme-switcher-content', $crawler->filter('#dropdown-menu-admin-theme-switcher-trigger')->attr('aria-controls'));

        $options = $crawler->filter('[role="menuitemradio"]');
        self::assertSame(['light', 'dark', 'auto'], $options->each(static fn ($option): string => (string) $option->attr('data-admin--theme-switcher-mode-param')));
        // The test application runs in French: the labels come from the sylius.ui.theme_* translations.
        self::assertSame(['Clair', 'Sombre', 'Auto'], $options->each(static fn ($option): string => trim($option->text())));

        $auto = $options->last();
        self::assertSame('option', $auto->attr('data-admin--theme-switcher-target'));
        self::assertSame('true', $auto->attr('data-close-on-select'));
        self::assertStringContainsString('shadcn--dropdown-menu#selectRadio', (string) $auto->attr('data-action'));
        self::assertStringContainsString('admin--theme-switcher#select', (string) $auto->attr('data-action'));
    }

    public function testTheInlineScriptFollowsTheOsPreferenceInAutoMode(): void
    {
        $html = $this->render(<<<'TWIG'
            {% include '@SyliusShadcnAdminUi/shared/layout/theme.html.twig' %}
            TWIG);

        self::assertStringContainsString("localStorage.getItem('sylius-theme')", $html);
        self::assertStringContainsString('root.dataset.themeMode = mode', $html);
        self::assertStringContainsString("media.addEventListener('change'", $html);
    }
}
