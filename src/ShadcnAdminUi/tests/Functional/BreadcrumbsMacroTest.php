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

final class BreadcrumbsMacroTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testItRendersLinksAndTheCurrentPage(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% from '@SyliusShadcnAdminUi/shared/helper/breadcrumbs.html.twig' import breadcrumbs %}
            {{ breadcrumbs([{name: 'Dashboard', url: '/admin'}, null, {name: 'Speakers', url: '/admin/speakers'}, {name: 'Julien', active: true}, {name: 'Edit'}]) }}
            TWIG);

        self::assertSame(4, $crawler->filter('[data-slot="breadcrumb-item"]')->count());
        self::assertSame(3, $crawler->filter('[data-slot="breadcrumb-separator"]')->count());
        self::assertSame(['/admin', '/admin/speakers'], $crawler->filter('a[data-slot="breadcrumb-link"]')->each(static fn ($link): string => (string) $link->attr('href')));
        self::assertSame('Julien', trim($crawler->filter('[data-slot="breadcrumb-item"]')->eq(2)->text()));
        self::assertSame(['Edit'], $crawler->filter('[aria-current="page"]')->each(static fn ($page): string => trim($page->text())));
    }

    public function testAnActiveItemIsNotALink(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            {% from '@SyliusShadcnAdminUi/shared/helper/breadcrumbs.html.twig' import breadcrumbs %}
            {{ breadcrumbs([{name: 'Dashboard', url: '/admin'}, {name: 'Books', url: '/admin/books', active: true}]) }}
            TWIG);

        self::assertSame(1, $crawler->filter('a[data-slot="breadcrumb-link"]')->count());
        self::assertSame('Books', trim($crawler->filter('[data-slot="breadcrumb-page"][aria-current="page"]')->text()));
    }
}
