<?php

declare(strict_types=1);

namespace MainTests\Sylius\Translations;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class FrenchTranslatedLoginTest extends WebTestCase
{
    use MarkTestSkippedTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testInvalidCredentialsMessageIsTranslated(): void
    {
        $this->markTestSkippedIfNecessary('fr');

        $this->client->request('POST', '/admin/login_check', [
            '_username' => 'nonexistent@example.com',
            '_password' => 'wrong-password',
        ]);
        $this->client->followRedirect();

        self::assertSelectorTextContains('[data-test-invalid-credentials-message]', 'Identifiants invalides.');
    }
}
