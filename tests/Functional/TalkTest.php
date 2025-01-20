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

use App\Enum\Track;
use App\Factory\ConferenceFactory;
use App\Factory\SpeakerFactory;
use App\Factory\TalkFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TalkTest extends WebTestCase
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

    public function testBrowsingTalks(): void
    {
        TalkFactory::new()
            ->withTitle('Boost Your Sylius Frontend with Hotwire, aka Symfony UX')
            ->withSpeakers(SpeakerFactory::findOrCreate(['firstName' => 'Loïc', 'lastName' => 'Caillieux']))
            ->withStartingDate(new \DateTimeImmutable('2024-11-13 10:00:00'))
            ->withEndingDate(new \DateTimeImmutable('2024-11-13 10:45:00'))
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Admin Panel (R)evolution for Your Symfony Projects')
            ->withSpeakers(SpeakerFactory::findOrCreate(['firstName' => 'Loïc', 'lastName' => 'Frémont']))
            ->withStartingDate(new \DateTimeImmutable('2024-11-13 11:00:00'))
            ->withEndingDate(new \DateTimeImmutable('2024-11-13 11:45:00'))
            ->create()
        ;

        $this->client->request('GET', '/admin/talks');

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Talks');
        self::assertSelectorExists('a:contains("Create")');

        // Validate Table header
        self::assertSelectorTextContains('.sylius-table-column-title', 'Title');
        self::assertSelectorTextContains('.sylius-table-column-speakers', 'Speaker');
        self::assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        self::assertSelectorTextContains('tr.item:first-child', 'Boost Your Sylius Frontend with Hotwire, aka Symfony UX');
        self::assertSelectorTextContains('tr.item:first-child', 'Loïc Caillieux');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        self::assertSelectorTextContains('tr.item:last-child', 'Admin Panel (R)evolution for Your Symfony Projects');
        self::assertSelectorTextContains('tr.item:last-child', 'Loïc Frémont');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        self::assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
    }

    public function testAddingTalk(): void
    {
        $conference = ConferenceFactory::new()
            ->withName('SyliusCon 2024')
            ->create()
        ;

        $this->client->request('GET', '/admin/talks/new');

        $this->client->submitForm('Create', [
            'talk[title]' => 'Boost Your Sylius Frontend with Hotwire, aka Symfony UX',
            'talk[conference]' => $conference->getId(),
            'talk[startsAt]' => '2024-11-13T10:00',
            'talk[endsAt]' => '2024-11-13T10:45',
            'talk[track]' => Track::TECH_TWO->value,
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/talks');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Talk has been successfully created.');

        $talk = TalkFactory::find(['title' => 'Boost Your Sylius Frontend with Hotwire, aka Symfony UX']);

        self::assertSame('Boost Your Sylius Frontend with Hotwire, aka Symfony UX', $talk->getTitle());
        self::assertSame('SyliusCon 2024', $talk->getConference()?->getName());
        self::assertSame('2024-11-13 10:00', $talk->getStartsAt()?->format('Y-m-d H:i'));
        self::assertSame('2024-11-13 10:45', $talk->getEndsAt()?->format('Y-m-d H:i'));
    }

    public function testEditingTalk(): void
    {
        $talk = TalkFactory::new()
            ->withTitle('Boost Your Sylius Frontend with Hotwire, aka Symfony UX')
            ->withTrack(Track::TECH_TWO)
            ->create();

        $this->client->request('GET', sprintf('/admin/talks/%s/edit', $talk->getId()));

        $this->client->submitForm('Update', [
            'talk[title]' => 'Boost Your Sylius Frontend with Symfony UX',
            'talk[track]' => Track::TECH_ONE->value,
        ]);

        self::assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/books');

        // Test flash message
        self::assertSelectorTextContains('[data-test-sylius-flash-message]', 'Talk has been successfully updated.');

        $talk = TalkFactory::find(['title' => 'Boost Your Sylius Frontend with Symfony UX']);

        self::assertSame('Boost Your Sylius Frontend with Symfony UX', $talk->getTitle());
        self::assertSame(Track::TECH_ONE->value, $talk->getTrack()?->value);
    }

    public function testValidationErrorsWhenEditingTalk(): void
    {
        $talk = TalkFactory::createOne();

        $this->client->request('GET', sprintf('/admin/talks/%s/edit', $talk->getId()));
        $this->client->submitForm('Update', [
            'talk[title]' => null,
            'talk[conference]' => '',
            'talk[track]' => '',
            'talk[startsAt]' => null,
            'talk[endsAt]' => null,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        self::assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        self::assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        self::assertSelectorTextContains('#talk_title + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#talk_conference + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#talk_track + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#talk_startsAt + .invalid-feedback', 'This value should not be blank.');
        self::assertSelectorTextContains('#talk_endsAt + .invalid-feedback', 'This value should not be blank.');
    }
}
