<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Entity\Book;
use App\Factory\BookFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Attribute\ResetDatabase;
use Zenstruck\Foundry\Persistence\Proxy;

#[ResetDatabase]
final class BookTest extends WebTestCase
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

    public function testShowingBook(): void
    {
        $book = BookFactory::new()
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        $this->client->request('GET', sprintf('/admin/books/%s', $book->getId()));

        $this->assertResponseIsSuccessful();

        // Validate Header
        $this->assertSelectorTextContains('[data-test-page-title]', 'The Shining');
        $this->assertSelectorTextContains('[data-test-subheader]', 'Stephen King');
        $this->assertSelectorExists('[data-test-icon="tabler:book"]');

        // Validate page body
        $this->assertSelectorTextContains('[data-test-author-name]', 'Stephen King');
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

        $this->client->request('GET', '/admin/books');

        $this->assertResponseIsSuccessful();

        // Validate Header
        $this->assertSelectorTextContains('[data-test-page-title]', 'Books');
        $this->assertSelectorExists('a:contains("Create")');

        // Validate Custom Twig Hooks
        $this->assertSelectorTextContains('[data-test-book-grid-description]', 'Aliquam arcu ligula, aliquet vitae malesuada quis');

        // Validate Table header
        $this->assertSelectorTextContains('.sylius-table-column-title', 'Title');
        $this->assertSelectorTextContains('.sylius-table-column-authorName', 'Author name');
        $this->assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        $this->assertSelectorTextContains('tr.item:first-child', 'Carrie');
        $this->assertSelectorTextContains('tr.item:first-child', 'Stephen King');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Show]');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        $this->assertSelectorTextContains('tr.item:last-child', 'The Shining');
        $this->assertSelectorTextContains('tr.item:last-child', 'Stephen King');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Show]');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
    }

    public function testBrowsingBooksWithoutGrid(): void
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

        $this->client->request('GET', '/admin/books/withoutGrid');

        $this->assertResponseIsSuccessful();

        // Validate Header
        $this->assertSelectorTextContains('[data-test-page-title]', 'Books');
    }

    public function testSortingBooks(): void
    {
        BookFactory::new()
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create();

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create();

        $crawler = $this->client->request('GET', '/admin/books');

        $link = $crawler->filter('.sylius-table-column-title a')->link();
        $this->client->request('GET', $link->getUri());

        $this->assertResponseIsSuccessful();

        // Validate it's sorted by title desc
        $this->assertSelectorTextContains('tr.item:first-child', 'The Shining');
        $this->assertSelectorTextContains('tr.item:last-child', 'Carrie');
    }

    public function testFilteringBooks(): void
    {
        BookFactory::new()
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create();

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', '/admin/books');

        $this->client->submitForm(button: 'Filter', fieldValues: [
            'criteria[search][value]' => 'Shin',
        ], method: 'GET');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorCount(1, 'tr.item');
        $this->assertSelectorTextContains('tr.item:first-child', 'The Shining');
    }

    public function testAddingBookContent(): void
    {
        $this->client->request('GET', '/admin/books/new');

        $this->assertResponseIsSuccessful();

        $this->assertInputValueSame('sylius_resource[title]', '');
        $this->assertInputValueSame('sylius_resource[authorName]', '');
    }

    public function testAddingBook(): void
    {
        $this->client->request('GET', '/admin/books/new');

        $this->client->submitForm('Create', [
            'sylius_resource[title]' => 'The Shining',
            'sylius_resource[authorName]' => 'Stephen King',
        ]);

        $this->assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/books');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book has been successfully created.');

        /** @var Proxy<Book> $book */
        $book = BookFactory::find(['title' => 'The Shining']);

        $this->assertSame('The Shining', $book->getTitle());
        $this->assertSame('Stephen King', $book->getAuthorName());
    }

    public function testValidationErrorsWhenAddingBook(): void
    {
        $this->client->request('GET', '/admin/books/new');
        $this->client->submitForm('Create', [
            'sylius_resource[title]' => null,
            'sylius_resource[authorName]' => null,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        $this->assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        $this->assertSelectorTextContains('#sylius_resource_title + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#sylius_resource_authorName + .invalid-feedback', 'This value should not be blank.');
    }

    public function testEditingBookContent(): void
    {
        $book = BookFactory::new()
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));

        $this->assertResponseIsSuccessful();

        $this->assertInputValueSame('sylius_resource[title]', 'The Shining');
        $this->assertInputValueSame('sylius_resource[authorName]', 'Stephen King');
    }

    public function testEditingBook(): void
    {
        $book = BookFactory::new()
            ->withTitle('The Shining')
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
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));
        $this->client->submitForm('Update', [
            'sylius_resource[title]' => null,
            'sylius_resource[authorName]' => null,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        $this->assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        $this->assertSelectorTextContains('#sylius_resource_title + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#sylius_resource_authorName + .invalid-feedback', 'This value should not be blank.');
    }

    public function testRemovingBook(): void
    {
        BookFactory::new()
            ->withTitle('The Shining')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', '/admin/books');
        $deleteButton = $this->client->getCrawler()->filter('tr.item:first-child [data-test-confirm-button]');

        $this->client->submit($deleteButton->form());

        $this->assertResponseRedirects();

        $this->client->request('GET', '/admin/books');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book has been successfully deleted.');

        $this->assertCount(0,  BookFactory::all());
    }

    public function testShowingMenu(): void
    {
        $this->client->request('GET', '/admin/books');

        self::assertSelectorExists('#sidebar-menu');

        self::assertAnySelectorTextContains('#sidebar-menu [href="#navbar-library"]', 'Library');
        self::assertAnySelectorTextContains('#sidebar-menu [href="#navbar-configuration"]', 'Configuration');
        self::assertAnySelectorTextContains('#sidebar-menu [href="/admin/conferences"]', 'Conferences');
        self::assertAnySelectorTextContains('#sidebar-menu [href="/admin/talks"]', 'Talks');
        self::assertAnySelectorTextContains('#sidebar-menu [href="/admin/speakers"]', 'Speakers');
    }
}
