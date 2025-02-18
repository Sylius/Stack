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

namespace App\Story;

use App\Factory\ConferenceFactory;
use DateTimeImmutable;
use Zenstruck\Foundry\Story;

final class DefaultConferencesStory extends Story
{
    public function build(): void
    {
        ConferenceFactory::new()
            ->withName('SyliusCon 2024')
            ->withStartingDate(new DateTimeImmutable('2024-11-13 09:00:00'))
            ->withEndingDate(new DateTimeImmutable('2024-11-13 18:00:00'))
            ->pastEvent(false)
            ->create()
        ;

        ConferenceFactory::new()
            ->withName('SyliusCon 2023')
            ->withStartingDate(new DateTimeImmutable('2023-11-03 09:00:00'))
            ->withEndingDate(new DateTimeImmutable('2023-11-03 18:00:00'))
            ->pastEvent(true)
            ->withArchivingDate(new DateTimeImmutable('2024-01-01 00:00:00'))
            ->create()
        ;

        ConferenceFactory::new()->withStartingDate(new \DateTimeImmutable('first day of january this year'))->many(3)->create();
        ConferenceFactory::new()->withStartingDate(new \DateTimeImmutable('today'))->many(2)->create();

        ConferenceFactory::createMany(100);
    }
}
