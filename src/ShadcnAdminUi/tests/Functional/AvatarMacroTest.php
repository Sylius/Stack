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

final class AvatarMacroTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testItRendersAVisibleImageWithoutFallback(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/avatar.html.twig' as avatar %}
            {{ avatar.default('/avatar.jpg', 'my-avatar', 'jd') }}
            TWIG);

        $root = $crawler->filter('[data-slot="avatar"]');
        self::assertSame('default', $root->attr('data-size'));
        self::assertStringContainsString('my-avatar', (string) $root->attr('class'));
        $image = $crawler->filter('img[data-slot="avatar-image"]');
        self::assertSame('/avatar.jpg', $image->attr('src'));
        self::assertNotContains('hidden', explode(' ', (string) $image->attr('class')));
        self::assertCount(0, $crawler->filter('[data-slot="avatar-fallback"]'));
    }

    public function testItRendersTheInitialsWithoutImage(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/avatar.html.twig' as avatar %}
            {{ avatar.default(null, '', 'jd') }}
            TWIG);

        self::assertCount(0, $crawler->filter('img'));
        self::assertSame('jd', trim($crawler->filter('[data-slot="avatar-fallback"]')->text()));
    }

    public function testItRendersAnIconWithoutImageNorInitials(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/avatar.html.twig' as avatar %}
            {{ avatar.default(null, '') }}
            TWIG);

        self::assertCount(0, $crawler->filter('img'));
        self::assertCount(1, $crawler->filter('[data-slot="avatar-fallback"] svg'));
    }

    public function testSmallRendersTheSmallSize(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/avatar.html.twig' as avatar %}
            {{ avatar.small(null, '', 'JD') }}
            TWIG);

        self::assertSame('sm', $crawler->filter('[data-slot="avatar"]')->attr('data-size'));
        self::assertSame('JD', trim($crawler->filter('[data-slot="avatar-fallback"]')->text()));
    }
}
