<?php

declare(strict_types=1);

namespace Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class LoginTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testLoginContent(): void
    {
        $this->client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();

        // Validate Header
        $this->assertSelectorTextContains('h2', 'Login to your account');

        // Validate page body
        $this->assertSelectorExists('#_username');
        $this->assertSelectorExists('#_password');
    }

    public function testLoginSuccess(): void
    {
        UserFactory::new()
            ->withEmail('admin@example.com')
            ->withPassword('password')
            ->admin()
            ->create()
        ;

        $this->client->request('GET', '/admin/login');

        $this->client->submitForm('Login', [
            '_username' => 'admin@example.com',
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects(expectedLocation: '/admin/conferences', expectedCode: Response::HTTP_FOUND);
    }
}
