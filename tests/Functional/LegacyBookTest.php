<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Factory\BookFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class LegacyBookTest extends WebTestCase
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

    public function testBrowsingBooks(): void
    {
        BookFactory::new()
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        $this->client->request('GET', '/admin/legacy/books');

        $this->assertResponseIsSuccessful();
    }

    public function testAddingBookContent(): void
    {
        $this->client->request('GET', '/admin/legacy/books/new');

        $this->assertResponseIsSuccessful();
    }

    public function testEditingBookContent(): void
    {
        $book = BookFactory::new()
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/legacy/books/%s/edit', $book->getId()));

        $this->assertResponseIsSuccessful();
    }
}
