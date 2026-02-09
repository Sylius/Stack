<?php

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
use Zenstruck\Foundry\Attribute\ResetDatabase;

#[ResetDatabase]
final class TalkTest extends WebTestCase
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

        $this->assertResponseIsSuccessful();

        // Validate Header
        $this->assertSelectorTextContains('[data-test-page-title]', 'Talks');
        $this->assertSelectorExists('a:contains("Create")');

        // Validate Table header
        $this->assertSelectorTextContains('.sylius-table-column-title', 'Title');
        $this->assertSelectorTextContains('.sylius-table-column-speakers', 'Speaker');
        $this->assertSelectorTextContains('.sylius-table-column-actions', 'Actions');

        // Validate Table data
        $this->assertSelectorTextContains('tr.item:first-child', 'Boost Your Sylius Frontend with Hotwire, aka Symfony UX');
        $this->assertSelectorTextContains('tr.item:first-child', 'Loïc Caillieux');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:first-child [data-bs-title=Delete]');

        $this->assertSelectorTextContains('tr.item:last-child', 'Admin Panel (R)evolution for Your Symfony Projects');
        $this->assertSelectorTextContains('tr.item:last-child', 'Loïc Frémont');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Edit]');
        $this->assertSelectorExists('tr.item:last-child [data-bs-title=Delete]');
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

        $this->assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/talks');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Talk has been successfully created.');

        $talk = TalkFactory::find(['title' => 'Boost Your Sylius Frontend with Hotwire, aka Symfony UX']);

        $this->assertSame('Boost Your Sylius Frontend with Hotwire, aka Symfony UX', $talk->getTitle());
        $this->assertSame('SyliusCon 2024', $talk->getConference()?->getName());
        $this->assertSame('2024-11-13 10:00', $talk->getStartsAt()?->format('Y-m-d H:i'));
        $this->assertSame('2024-11-13 10:45', $talk->getEndsAt()?->format('Y-m-d H:i'));
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

        $this->assertResponseRedirects(expectedCode: Response::HTTP_FOUND);

        $this->client->request('GET', '/admin/books');

        // Test flash message
        $this->assertSelectorTextContains('[data-test-sylius-flash-message]', 'Talk has been successfully updated.');

        $talk = TalkFactory::find(['title' => 'Boost Your Sylius Frontend with Symfony UX']);

        $this->assertSame('Boost Your Sylius Frontend with Symfony UX', $talk->getTitle());
        $this->assertSame(Track::TECH_ONE->value, $talk->getTrack()?->value);
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

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSelectorTextContains('[data-test-form-error-alert] .alert-title', 'Error');
        $this->assertSelectorTextContains('[data-test-form-error-alert] .text-secondary', 'This form contains errors.');
        $this->assertSelectorTextContains('#talk_title + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#talk_conference + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#talk_track + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#talk_startsAt + .invalid-feedback', 'This value should not be blank.');
        $this->assertSelectorTextContains('#talk_endsAt + .invalid-feedback', 'This value should not be blank.');
    }
}
