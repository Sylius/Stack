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

namespace Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class LoginTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testLoginContent(): void
    {
        $this->client->request('GET', '/admin/login');

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h2', 'Login to your account');

        // Validate page body
        self::assertSelectorExists('#_username');
        self::assertSelectorExists('#_password');
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

        self::assertResponseRedirects(expectedLocation: '/admin/conferences', expectedCode: Response::HTTP_FOUND);
    }
}
