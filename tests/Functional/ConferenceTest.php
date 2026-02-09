<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Factory\ConferenceFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class ConferenceTest extends WebTestCase
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

        $this->assertResponseIsSuccessful();

        // Validate Header
        $this->assertSelectorTextContains('[data-test-page-title]', 'Conferences');
        $this->assertSelectorExists('a:contains("Create")');

        // Validate Table header
        $this->assertSelectorTextContains('.sylius-table-column-name', 'Name');
        $this->assertSelectorTextContains('.sylius-table-column-startsAt', 'Starts at');
        $this->assertSelectorTextContains('.sylius-table-column-endsAt', 'Ends at');
        $this->assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        $this->assertSelectorTextContains('tr.item:first-child', 'SyliusCon 2024');
        $this->assertSelectorTextContains('tr.item:first-child', '2024-11-13 09:00:00');
        $this->assertSelectorTextContains('tr.item:first-child', '2024-11-13 18:00:00');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        $this->assertSelectorTextContains('tr.item:last-child', 'SyliusCon 2023');
        $this->assertSelectorTextContains('tr.item:last-child', '2023-11-03 09:00:00');
        $this->assertSelectorTextContains('tr.item:last-child', '2023-11-03 18:00:00');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
    }

    public function testAddingConferenceContent(): void
    {
        $this->client->request('GET', '/admin/conferences/new');

        // Test header
        $this->assertSelectorTextContains('[data-test-page-title]', 'New conference');
        $this->assertSelectorExists('[data-test-icon="tabler:plus"]');
        $this->assertSelectorTextContains('[data-test-subheader]', 'Managing your conferences');
    }

    public function testAddingConference(): void
    {
        $this->client->request('GET', '/admin/conferences/new');

        $this->client->submitForm('Create', [
            'conference[name]' => 'SyliusCon 2024',
            'conference[startsAt]' => '2024-11-13T09:00',
            'conference[endsAt]' => '2024-11-13T18:00',
        ]);

        $this->assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/conferences');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Conference has been successfully created.');

        $conference = ConferenceFactory::find(['name' => 'SyliusCon 2024']);

        $this->assertSame('SyliusCon 2024', $conference->getName());
        $this->assertSame('2024-11-13 09:00', $conference->getStartsAt()?->format('Y-m-d H:i'));
        $this->assertSame('2024-11-13 18:00', $conference->getEndsAt()?->format('Y-m-d H:i'));
    }

    public function testEditingConferenceContent(): void
    {
        $conference = ConferenceFactory::new()
            ->withName('SyliusCon 2023')
            ->create();

        $this->client->request('GET', sprintf('/admin/conferences/%s/edit', $conference->getId()));

        // Test header
        $this->assertSelectorTextContains('[data-test-page-title]', 'Edit conference');
        $this->assertSelectorExists('[data-test-icon="tabler:pencil"]');
        $this->assertSelectorTextContains('[data-test-subheader]', 'Managing your conferences');
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

        $this->assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/conferences');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Conference has been successfully updated.');

        $conference = ConferenceFactory::find(['name' => 'SyliusCon 2024']);

        $this->assertSame('SyliusCon 2024', $conference->getName());
        $this->assertSame('2024-11-13 09:00', $conference->getStartsAt()?->format('Y-m-d H:i'));
        $this->assertSame('2024-11-13 18:00', $conference->getEndsAt()?->format('Y-m-d H:i'));
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

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        $this->assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        $this->assertSelectorTextContains('#conference_name + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#conference_startsAt + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#conference_endsAt + .invalid-feedback', 'This value should not be blank.');
    }
}
