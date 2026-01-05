<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class DashboardTest extends WebTestCase
{
    Use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $user = UserFactory::new()
            ->admin()
            ->create()
        ;

        $this->client->loginUser($user);
    }

    public function testDashboard(): void
    {
        $this->client->request('GET', '/admin/');

        self::assertResponseIsSuccessful();

        self::assertSelectorTextContains('[data-test-page-title]', 'Dashboard');
    }
}
