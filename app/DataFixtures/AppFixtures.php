<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\DefaultBooksStory;
use App\Story\DefaultSpeakersStory;
use App\Story\DefaultTalksStory;
use App\Story\DefaultUsersStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultUsersStory::load();
        DefaultBooksStory::load();
        DefaultSpeakersStory::load();
        DefaultTalksStory::load();
    }
}
