<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class DashboardTest extends WebTestCase
{
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

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('[data-test-page-title]', 'Dashboard');
    }
}
