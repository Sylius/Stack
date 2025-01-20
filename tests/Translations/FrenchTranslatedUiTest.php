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

namespace MainTests\Sylius\Translations;

use App\Entity\Book;
use App\Factory\BookFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class FrenchTranslatedUiTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;
    use MarkTestSkippedTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $user = UserFactory::new()
            ->admin()
            ->create()
        ;

        $this->client->loginUser($user->_real());
    }

    public function testShowItem(): void
    {
        $this->markTestSkippedIfNecessary('fr');

        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        $this->client->request('GET', sprintf('/admin/books/%s', $book->getId()));

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Afficher Livre');
    }

    public function testBrowsingItems(): void
    {
        $this->markTestSkippedIfNecessary('fr');

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
        self::assertSelectorTextContains('h1.page-title', 'Livres');

        // Validate Table header
        self::assertSelectorTextContains('.sylius-table-column-title', 'Titre');
        self::assertSelectorTextContains('.sylius-table-column-authorName', 'Auteur');
        self::assertSelectorTextContains('.sylius-table-column-actions', 'Actions');
    }

    public function testAddingNewItem(): void
    {
        $this->markTestSkippedIfNecessary('fr');

        $this->client->request('GET', '/admin/books/new');

        $this->client->submitForm('Créer', [
            'sylius_resource[title]' => 'Shinning',
            'sylius_resource[authorName]' => 'Stephen King',
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/books');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book a bien été créé.');
    }

    public function testValidationErrorsWhenAddingNewItem(): void
    {
        $this->markTestSkippedIfNecessary('fr');

        $this->client->request('GET', '/admin/books/new');
        $this->client->submitForm('Créer', [
            'sylius_resource[title]' => null,
            'sylius_resource[authorName]' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Erreur');
        self::assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'Ce formulaire contient des erreurs.');
        self::assertSelectorTextContains('#sylius_resource_title + .invalid-feedback', 'Cette valeur ne doit pas être vide.');
        self::assertSelectorTextContains('#sylius_resource_authorName + .invalid-feedback', 'Cette valeur ne doit pas être vide.');
    }

    public function testEditingItem(): void
    {
        $this->markTestSkippedIfNecessary('fr');

        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));

        $this->client->submitForm('Mise à jour', [
            'sylius_resource[title]' => 'Carrie',
            'sylius_resource[authorName]' => 'Stephen King',
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/books');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book a bien été mis à jour.');

        /** @var Proxy<Book> $book */
        $book = BookFactory::find(['title' => 'Carrie']);

        self::assertSame('Carrie', $book->getTitle());
        self::assertSame('Stephen King', $book->getAuthorName());
    }

    public function testValidationErrorsWhenEditingItem(): void
    {
        $this->markTestSkippedIfNecessary('fr');

        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));
        $this->client->submitForm('Mise à jour', [
            'sylius_resource[title]' => null,
            'sylius_resource[authorName]' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Erreur');
        self::assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'Ce formulaire contient des erreurs.');
        self::assertSelectorTextContains('#sylius_resource_title + .invalid-feedback', 'Cette valeur ne doit pas être vide.');
        self::assertSelectorTextContains('#sylius_resource_authorName + .invalid-feedback', 'Cette valeur ne doit pas être vide.');
    }

    public function testRemovingItem(): void
    {
        $this->markTestSkippedIfNecessary('fr');

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
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Book a bien été supprimé.');
    }
}
