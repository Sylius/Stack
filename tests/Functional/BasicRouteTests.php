<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class BasicRouteTests extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testCreate(): void
    {
        $this->client->request('GET', '/basic/create');

        $this->assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        $this->client->request('GET', '/basic/update');

        $this->assertResponseIsSuccessful();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/basic/index');

        $this->assertResponseIsSuccessful();
    }

    public function testShow(): void
    {
        $this->client->request('GET', '/basic/show');

        $this->assertResponseIsSuccessful();
    }
}
