<?php

declare(strict_types=1);

namespace Functional;

use App\Factory\SpeakerFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class SpeakerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = $this->createClient();

        $user = UserFactory::new()
            ->admin()
            ->create()
        ;

        $this->client->loginUser($user);
    }

    public function testBrowsingSpeakers(): void
    {
        SpeakerFactory::new()
            ->withFirstName('Francis')
            ->withLastName('Hilaire')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Gregor')
            ->withLastName('Šink')
            ->create()
        ;

        $this->client->request('GET', '/admin/speakers');

        $this->assertResponseIsSuccessful();

        // Validate Header
        $this->assertSelectorTextContains('[data-test-page-title]', 'Browsing speakers');
        $this->assertSelectorExists('[data-test-icon="tabler:users"]');
        $this->assertSelectorTextContains('[data-test-subheader]', 'Managing your speakers');
        $this->assertSelectorExists('a:contains("Create")');

        // Validate Table header
        $this->assertSelectorTextContains('.sylius-table-column-firstName', 'First name');
        $this->assertSelectorTextContains('.sylius-table-column-lastName', 'Last name');
        $this->assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        $this->assertSelectorTextContains('tr.item:first-child', 'Francis');
        $this->assertSelectorTextContains('tr.item:first-child', 'Hilaire');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        $this->assertSelectorTextContains('tr.item:last-child', 'Gregor');
        $this->assertSelectorTextContains('tr.item:last-child', 'Šink');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
    }

    public function testAddingSpeaker(): void
    {
        $this->client->request('GET', '/admin/speakers/new');

        $this->client->submitForm('Create', [
            'speaker[firstName]' => 'Loïc',
            'speaker[lastName]' => 'Caillieux',
            'speaker[companyName]' => 'Emagma',
        ]);

        $this->assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/speakers');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Speaker has been successfully created.');

        $speaker = SpeakerFactory::find(['firstName' => 'Loïc']);

        $this->assertSame('Loïc', $speaker->getFirstName());
        $this->assertSame('Caillieux', $speaker->getLastName());
        $this->assertSame('Emagma', $speaker->getCompanyName());
    }

    public function testEditingSpeaker(): void
    {
        $speaker = SpeakerFactory::new()
            ->withFirstName('Loïc')
            ->withLastName('Frémont')
            ->create();

        $this->client->request('GET', sprintf('/admin/speakers/%s/edit', $speaker->getId()));

        $this->client->submitForm('Update', [
            'speaker[lastName]' => 'Caillieux',
        ]);

        $this->assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/speakers');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Speaker has been successfully updated.');

        $speaker = SpeakerFactory::find(['firstName' => 'Loïc']);

        $this->assertSame('Loïc', $speaker->getFirstName());
        $this->assertSame('Caillieux', $speaker->getLastName());
    }

    public function testValidationErrorsWhenEditingSpeaker(): void
    {
        $speaker = SpeakerFactory::createOne();

        $this->client->request('GET', sprintf('/admin/speakers/%s/edit', $speaker->getId()));
        $this->client->submitForm('Update', [
            'speaker[firstName]' => null,
            'speaker[lastName]' => null,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        $this->assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        $this->assertSelectorTextContains('#speaker_firstName + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#speaker_lastName + .invalid-feedback', 'This value should not be blank.');
    }
}
