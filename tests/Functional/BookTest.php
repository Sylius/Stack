<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Entity\Book;
use App\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class BookTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testBrowsingBooks(): void
    {
        BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        $this->client->request('GET', '/admin/books');

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Library');

        // Validate Table header
        self::assertSelectorTextContains('.sylius-table-column-title', 'Title');
        self::assertSelectorTextContains('.sylius-table-column-authorName', 'Author Name');
        self::assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        self::assertSelectorTextContains('tr.item:first-child', 'Carrie');
        self::assertSelectorTextContains('tr.item:first-child', 'Stephen King');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        self::assertSelectorTextContains('tr.item:last-child', 'Shinning');
        self::assertSelectorTextContains('tr.item:last-child', 'Stephen King');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
    }

    public function testSortingBooks(): void
    {
        BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create();

        $crawler = $this->client->request('GET', '/admin/books');

        self::assertResponseIsSuccessful();

        $link = $crawler->filter('.sylius-table-column-title a')->link();
        $this->client->request('GET', $link->getUri());

        self::assertResponseIsSuccessful();

        // Validate it's sorted by title desc
        self::assertSelectorTextContains('tr.item:first-child', 'Shinning');
        self::assertSelectorTextContains('tr.item:last-child', 'Carrie');
    }

    public function testAddingBookContent(): void
    {
        $this->client->request('GET', '/admin/books/new');

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'New Book');

        // Validate Form
        self::assertSelectorTextContains('label[for=book_title]', 'Title');
        self::assertSelectorExists('#book_title');

        self::assertSelectorTextContains('label[for=book_authorName]', 'Author name');
        self::assertSelectorExists('#book_authorName');

        // Validate Buttons
        self::assertSelectorExists('[type="submit"]:contains("Create")');
        self::assertSelectorExists('[data-test-cancel-changes-button]');
    }

    public function testAddingBook(): void
    {
        $this->client->request('GET', '/admin/books/new');

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Create', [
            'book[title]' => 'Shinning',
            'book[authorName]' => 'Stephen King',
        ]);

        self::assertResponseRedirects(null, expectedCode: Response::HTTP_FOUND);

        /** @var Book $book */
        $book = self::getContainer()->get('app.repository.book')->findOneBy(['title' => 'Shinning']);

        self::assertNotNull($book);
        self::assertSame('Shinning', $book->getTitle());
        self::assertSame('Stephen King', $book->getAuthorName());
    }

    public function testEditingBookContent(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Edit Book');

        // Validate Form
        self::assertSelectorTextContains('label[for=book_title]', 'Title');
        self::assertSelectorExists('#book_title');

        self::assertSelectorTextContains('label[for=book_authorName]', 'Author name');
        self::assertSelectorExists('#book_authorName');

        // Validate Buttons
        self::assertSelectorExists('[type="submit"]:contains("Update")');
        self::assertSelectorExists('[data-test-cancel-changes-button]');
    }

    public function testEditingBook(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));

        $this->client->submitForm('Update', [
            'book[title]' => 'Carrie',
            'book[authorName]' => 'Stephen King',
        ]);

        self::assertResponseRedirects(null, expectedCode: Response::HTTP_FOUND);

        $editedBook = $book->_refresh();

        self::assertInstanceOf(Book::class, $editedBook->_real());
        self::assertSame('Carrie', $editedBook->getTitle());
    }
}
