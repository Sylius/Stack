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
}
