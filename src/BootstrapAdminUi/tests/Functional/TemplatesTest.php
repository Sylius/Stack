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

namespace Tests\Sylius\BootstrapAdminUi\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TemplatesTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testIndexTemplate(): void
    {
        $this->client->request('GET', '/books');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'app.ui.books | Sylius');
        $this->assertSelectorTextContains('tr.item:first-child[data-test-resource-id]', 'The Shining');
        $this->assertSelectorTextContains('tr.item:last-child[data-test-resource-id]', 'Carrie');

        // Test the translation domain on menu
        $this->assertAnySelectorTextSame('.nav-link-title', 'Dashboard'); // with "messages" domain
        $this->assertAnySelectorTextSame('.nav-link-title', 'Library'); // with "menu" domain
    }

    public function testIndexTemplateHidesFieldsThatAreNotVisibleByDefault(): void
    {
        $this->client->request('GET', '/books');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-test-column-selector]');
        $this->assertSelectorExists('th.sylius-table-column-name');
        $this->assertSelectorNotExists('th.sylius-table-column-id');
        $this->assertSelectorExists('[data-test-column-selector-item="id"][aria-checked="false"]');
        $this->assertSelectorExists('[data-test-column-selector-item="name"].disabled[aria-disabled="true"]');
        $this->assertSelectorNotExists('[data-test-filters-form] input[name^="visibility"]');
    }

    public function testIndexTemplateShowsAndHidesFieldsFromTheVisibilityParameter(): void
    {
        $crawler = $this->client->request('GET', '/books?visibility[id]=visible&visibility[name]=hidden');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('th.sylius-table-column-name');
        $this->assertSelectorExists('th.sylius-table-column-id');
        $this->assertSelectorTextContains('tr.item:first-child[data-test-resource-id]', 'the-shining');
        $this->assertSelectorExists('[data-test-filters-form] input[type="hidden"][name="visibility[id]"][value="visible"]');
        $this->assertSelectorExists('[data-test-filters-form] input[type="hidden"][name="visibility[name]"][value="hidden"]');
        $this->assertSelectorExists('[data-test-column-selector-item="id"].disabled[aria-disabled="true"]');
        $this->assertSame(
            '/books?visibility%5Bid%5D=visible',
            $crawler->filter('[data-test-column-selector-item="name"]')->attr('href'),
        );
    }
}
