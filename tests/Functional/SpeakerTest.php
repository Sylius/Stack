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

namespace Functional;

use App\Factory\SpeakerFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class SpeakerTest extends WebTestCase
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

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Speakers');
        self::assertSelectorExists('a:contains("Create")');

        // Validate Table header
        self::assertSelectorTextContains('.sylius-table-column-firstName', 'First name');
        self::assertSelectorTextContains('.sylius-table-column-lastName', 'Last name');
        self::assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        self::assertSelectorTextContains('tr.item:first-child', 'Francis');
        self::assertSelectorTextContains('tr.item:first-child', 'Hilaire');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        self::assertSelectorTextContains('tr.item:last-child', 'Gregor');
        self::assertSelectorTextContains('tr.item:last-child', 'Šink');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
    }

    public function testAddingSpeaker(): void
    {
        $this->client->request('GET', '/admin/speakers/new');

        $this->client->submitForm('Create', [
            'speaker[firstName]' => 'Loïc',
            'speaker[lastName]' => 'Caillieux',
            'speaker[companyName]' => 'Emagma',
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/speakers');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Speaker has been successfully created.');

        $speaker = SpeakerFactory::find(['firstName' => 'Loïc']);

        self::assertSame('Loïc', $speaker->getFirstName());
        self::assertSame('Caillieux', $speaker->getLastName());
        self::assertSame('Emagma', $speaker->getCompanyName());
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

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/speakers');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Speaker has been successfully updated.');

        $speaker = SpeakerFactory::find(['firstName' => 'Loïc']);

        self::assertSame('Loïc', $speaker->getFirstName());
        self::assertSame('Caillieux', $speaker->getLastName());
    }

    public function testValidationErrorsWhenEditingSpeaker(): void
    {
        $speaker = SpeakerFactory::createOne();

        $this->client->request('GET', sprintf('/admin/speakers/%s/edit', $speaker->getId()));
        $this->client->submitForm('Update', [
            'speaker[firstName]' => null,
            'speaker[lastName]' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        self::assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        self::assertSelectorTextContains('#speaker_firstName + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#speaker_lastName + .invalid-feedback', 'This value should not be blank.');
    }
}
