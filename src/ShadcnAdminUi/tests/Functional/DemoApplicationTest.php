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

namespace Tests\Sylius\ShadcnAdminUi\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Security\Core\User\InMemoryUser;
use TestApplication\Sylius\ShadcnAdminUi\Entity\Book;

/**
 * Smoke-tests the demo application (tests/Application) served by docker-compose.
 *
 * @internal
 */
final class DemoApplicationTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        foreach ([['doctrine:schema:drop', '--force' => true], ['doctrine:schema:create'], ['app:load-books']] as $command) {
            $output = new BufferedOutput();
            self::assertSame(0, $application->run(new ArrayInput(['command' => array_shift($command)] + $command), $output), $output->fetch());
        }
    }

    public function testTheLoginPageRenders(): void
    {
        $this->client->request('GET', '/admin/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[name="_csrf_admin_security_token"]');
        self::assertStringContainsString('<script type="importmap"', (string) $this->client->getResponse()->getContent());
    }

    public function testTheAdminPanelRequiresALogin(): void
    {
        $this->client->request('GET', '/admin/');

        self::assertResponseRedirects('/admin/login');
    }

    public function testTheDashboardRendersTheStatsOfTheDemo(): void
    {
        $this->logIn();
        $this->client->request('GET', '/admin/');
        $html = (string) $this->client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Livres au catalogue', $html);
        self::assertStringContainsString('Vue d&#039;ensemble de la bibliothèque', $html);
        self::assertStringContainsString('data-slot="card"', $html);
        self::assertStringContainsString('Démo Sylius Stack', $html);
    }

    public function testTheBookGridRendersFiltersAndBadges(): void
    {
        $this->logIn();
        $this->client->request('GET', '/admin/books');
        $html = (string) $this->client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Clean Code', $html);
        self::assertStringContainsString('data-controller="admin--grid-selection"', $html);
        self::assertStringContainsString('data-slot="badge"', $html);
        self::assertStringContainsString('Science-fiction', $html);
    }

    public function testTheBookShowPageRendersTheDetailsHook(): void
    {
        $this->logIn();
        $this->client->request('GET', '/admin/books/' . $this->findBookId('Dune'));
        $html = (string) $this->client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Frank Herbert', $html);
        self::assertStringContainsString('Paul Atréides', $html);
    }

    public function testABookCanBeCreated(): void
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/books/new');
        self::assertResponseIsSuccessful();

        // The submit button lives in the page header, outside the <form> (form="..." attribute).
        $this->client->submit($crawler->filter('form[name="sylius_resource"]')->form(), [
            'sylius_resource[title]' => 'Ubik',
            'sylius_resource[authorName]' => 'Philip K. Dick',
            'sylius_resource[category]' => 'science-fiction',
        ]);

        self::assertResponseRedirects();
        self::assertNotNull($this->findBookId('Ubik'));
    }

    private function logIn(): void
    {
        $this->client->loginUser(new InMemoryUser('admin', 'admin', ['ROLE_ADMIN']));
    }

    private function findBookId(string $title): ?int
    {
        $book = self::getContainer()->get('doctrine.orm.entity_manager')->getRepository(Book::class)->findOneBy(['title' => $title]);

        return $book?->getId();
    }
}
