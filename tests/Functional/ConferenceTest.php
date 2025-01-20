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

use App\Factory\ConferenceFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ConferenceTest extends WebTestCase
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

    public function testBrowsingConferences(): void
    {
        ConferenceFactory::new()
            ->withName('SyliusCon 2024')
            ->withStartingDate(new \DateTimeImmutable('2024-11-13 09:00:00'))
            ->withEndingDate(new \DateTimeImmutable('2024-11-13 18:00:00'))
            ->create()
        ;

        ConferenceFactory::new()
            ->withName('SyliusCon 2023')
            ->withStartingDate(new \DateTimeImmutable('2023-11-03 09:00:00'))
            ->withEndingDate(new \DateTimeImmutable('2023-11-03 18:00:00'))
            ->create()
        ;

        $this->client->request('GET', '/admin/conferences');

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Conferences');
        self::assertSelectorExists('a:contains("Create")');

        // Validate Table header
        self::assertSelectorTextContains('.sylius-table-column-name', 'Name');
        self::assertSelectorTextContains('.sylius-table-column-startsAt', 'Starts at');
        self::assertSelectorTextContains('.sylius-table-column-endsAt', 'Ends at');
        self::assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        self::assertSelectorTextContains('tr.item:first-child', 'SyliusCon 2024');
        self::assertSelectorTextContains('tr.item:first-child', '2024-11-13 09:00:00');
        self::assertSelectorTextContains('tr.item:first-child', '2024-11-13 18:00:00');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        self::assertSelectorTextContains('tr.item:last-child', 'SyliusCon 2023');
        self::assertSelectorTextContains('tr.item:last-child', '2023-11-03 09:00:00');
        self::assertSelectorTextContains('tr.item:last-child', '2023-11-03 18:00:00');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
    }

    public function testAddingConference(): void
    {
        $this->client->request('GET', '/admin/conferences/new');

        $this->client->submitForm('Create', [
            'conference[name]' => 'SyliusCon 2024',
            'conference[startsAt]' => '2024-11-13T09:00',
            'conference[endsAt]' => '2024-11-13T18:00',
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/conferences');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Conference has been successfully created.');

        $conference = ConferenceFactory::find(['name' => 'SyliusCon 2024']);

        self::assertSame('SyliusCon 2024', $conference->getName());
        self::assertSame('2024-11-13 09:00', $conference->getStartsAt()?->format('Y-m-d H:i'));
        self::assertSame('2024-11-13 18:00', $conference->getEndsAt()?->format('Y-m-d H:i'));
    }

    public function testEditingConference(): void
    {
        $conference = ConferenceFactory::new()
            ->withName('SyliusCon 2023')
            ->create();

        $this->client->request('GET', sprintf('/admin/conferences/%s/edit', $conference->getId()));

        $this->client->submitForm('Update', [
            'conference[name]' => 'SyliusCon 2024',
            'conference[startsAt]' => '2024-11-13T09:00',
            'conference[endsAt]' => '2024-11-13T18:00',
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/conferences');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Conference has been successfully updated.');

        $conference = ConferenceFactory::find(['name' => 'SyliusCon 2024']);

        self::assertSame('SyliusCon 2024', $conference->getName());
        self::assertSame('2024-11-13 09:00', $conference->getStartsAt()?->format('Y-m-d H:i'));
        self::assertSame('2024-11-13 18:00', $conference->getEndsAt()?->format('Y-m-d H:i'));
    }

    public function testValidationErrorsWhenEditingConference(): void
    {
        $conference = ConferenceFactory::createOne();

        $this->client->request('GET', sprintf('/admin/conferences/%s/edit', $conference->getId()));
        $this->client->submitForm('Update', [
            'conference[name]' => null,
            'conference[startsAt]' => null,
            'conference[endsAt]' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        self::assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        self::assertSelectorTextContains('#conference_name + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#conference_startsAt + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#conference_endsAt + .invalid-feedback', 'This value should not be blank.');
    }
}
