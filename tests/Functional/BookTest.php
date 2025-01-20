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

namespace MainTests\Sylius\Functional;

use App\Entity\Book;
use App\Factory\BookFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Persistence\Proxy;
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

        $user = UserFactory::new()
            ->admin()
            ->create()
        ;

        $this->client->loginUser($user->_real());
    }

    public function testShowingBook(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        $this->client->request('GET', sprintf('/admin/books/%s', $book->getId()));

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Show Book');

        // Validate page body
        self::assertSelectorTextContains('[data-test-title]', 'Shinning');
        self::assertSelectorTextContains('[data-test-author-name]', 'Stephen King');
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
        self::assertSelectorTextContains('h1.page-title', 'Books');
        self::assertSelectorExists('a:contains("Create")');

        // Validate Custom Twig Hooks
        self::assertSelectorTextContains('[data-test-book-grid-description]', 'Aliquam arcu ligula, aliquet vitae malesuada quis');

        // Validate Table header
        self::assertSelectorTextContains('.sylius-table-column-title', 'Title');
        self::assertSelectorTextContains('.sylius-table-column-authorName', 'Author name');
        self::assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        self::assertSelectorTextContains('tr.item:first-child', 'Carrie');
        self::assertSelectorTextContains('tr.item:first-child', 'Stephen King');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Show]');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        self::assertSelectorTextContains('tr.item:last-child', 'Shinning');
        self::assertSelectorTextContains('tr.item:last-child', 'Stephen King');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Show]');
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

        $link = $crawler->filter('.sylius-table-column-title a')->link();
        $this->client->request('GET', $link->getUri());

        self::assertResponseIsSuccessful();

        // Validate it's sorted by title desc
        self::assertSelectorTextContains('tr.item:first-child', 'Shinning');
        self::assertSelectorTextContains('tr.item:last-child', 'Carrie');
    }

    public function testFilteringBooks(): void
    {
        BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', '/admin/books');

        $this->client->submitForm(button: 'Filter', fieldValues: [
            'criteria[search][value]' => 'Shinn',
        ], method: 'GET');

        self::assertResponseIsSuccessful();

        self::assertSelectorCount(1, 'tr.item');
        self::assertSelectorTextContains('tr.item:first-child', 'Shinning');
    }

    public function testAddingBookContent(): void
    {
        $this->client->request('GET', '/admin/books/new');

        self::assertResponseIsSuccessful();

        self::assertInputValueSame('sylius_resource[title]', '');
        self::assertInputValueSame('sylius_resource[authorName]', '');
    }

    public function testAddingBook(): void
    {
        $this->client->request('GET', '/admin/books/new');

        $this->client->submitForm('Create', [
            'sylius_resource[title]' => 'Shinning',
            'sylius_resource[authorName]' => 'Stephen King',
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/books');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book has been successfully created.');

        /** @var Proxy<Book> $book */
        $book = BookFactory::find(['title' => 'Shinning']);

        self::assertSame('Shinning', $book->getTitle());
        self::assertSame('Stephen King', $book->getAuthorName());
    }

    public function testValidationErrorsWhenAddingBook(): void
    {
        $this->client->request('GET', '/admin/books/new');
        $this->client->submitForm('Create', [
            'sylius_resource[title]' => null,
            'sylius_resource[authorName]' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        self::assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        self::assertSelectorTextContains('#sylius_resource_title + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#sylius_resource_authorName + .invalid-feedback', 'This value should not be blank.');
    }

    public function testEditingBookContent(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));

        self::assertResponseIsSuccessful();

        self::assertInputValueSame('sylius_resource[title]', 'Shinning');
        self::assertInputValueSame('sylius_resource[authorName]', 'Stephen King');
    }

    public function testEditingBook(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));

        $this->client->submitForm('Update', [
            'sylius_resource[title]' => 'Carrie',
            'sylius_resource[authorName]' => 'Stephen King',
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/books');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book has been successfully updated.');

        /** @var Proxy<Book> $book */
        $book = BookFactory::find(['title' => 'Carrie']);

        self::assertSame('Carrie', $book->getTitle());
        self::assertSame('Stephen King', $book->getAuthorName());
    }

    public function testValidationErrorsWhenEditingBook(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));
        $this->client->submitForm('Update', [
            'sylius_resource[title]' => null,
            'sylius_resource[authorName]' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        self::assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        self::assertSelectorTextContains('#sylius_resource_title + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#sylius_resource_authorName + .invalid-feedback', 'This value should not be blank.');
    }

    public function testRemovingBook(): void
    {
        BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', '/admin/books');
        $deleteButton = $this->client->getCrawler()->filter('tr.item:first-child [data-test-confirm-button]');

        $this->client->submit($deleteButton->form());

        self::assertResponseRedirects();

        $this->client->request('GET', '/admin/books');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book has been successfully deleted.');

        $this->assertCount(0, BookFactory::all());
    }
}
