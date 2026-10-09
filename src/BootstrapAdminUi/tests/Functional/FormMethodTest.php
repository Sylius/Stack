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

final class FormMethodTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testCreateFormIsPostedEvenWhenTheNewResourceAlreadyHasAnId(): void
    {
        $this->client->request('GET', '/books/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('form input[name="_method"]');
    }

    public function testUpdateFormIsPut(): void
    {
        $this->client->request('GET', '/books/the-shining/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form input[name="_method"][value="PUT"]');
    }
}
