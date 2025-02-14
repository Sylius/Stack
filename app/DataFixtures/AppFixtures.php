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

namespace App\DataFixtures;

use App\Story\DefaultBooksStory;
use App\Story\DefaultConferencesStory;
use App\Story\DefaultSpeakersStory;
use App\Story\DefaultSyliusCon2024TalksStory;
use App\Story\DefaultTalksStory;
use App\Story\DefaultUsersStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultBooksStory::load();
        DefaultConferencesStory::load();
        DefaultSpeakersStory::load();
        DefaultSyliusCon2024TalksStory::load();
        DefaultTalksStory::load();
        DefaultUsersStory::load();
    }
}
