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

use App\Factory\TalkFactory;
use function Zenstruck\Foundry\faker;
use Zenstruck\Foundry\Story;

final class DefaultTalksStory extends Story
{
    public function build(): void
    {
        TalkFactory::new()->withStartingDate(new \DateTimeImmutable('first day of january this year'))->many(3)->create();
        TalkFactory::new()->withStartingDate(new \DateTimeImmutable('today'))->many(2)->create();

        $this->createTalksSpreadOverLastAndCurrentYears();
    }

    /**
     * Creates a random number of talks for each month of the previous and the current years,
     * so the dashboard statistics chart is filled whatever the selected period.
     */
    private function createTalksSpreadOverLastAndCurrentYears(): void
    {
        $months = new \DatePeriod(
            new \DateTimeImmutable('first day of january last year'),
            new \DateInterval('P1M'),
            new \DateTimeImmutable('first day of january next year'),
        );

        foreach ($months as $firstDayOfMonth) {
            $lastDayOfMonth = $firstDayOfMonth->modify('last day of this month 23:59:59');

            for ($i = 0, $count = faker()->numberBetween(10, 40); $i < $count; ++$i) {
                $startsAt = \DateTimeImmutable::createFromMutable(
                    faker()->dateTimeBetween($firstDayOfMonth->format('Y-m-d H:i:s'), $lastDayOfMonth->format('Y-m-d H:i:s')),
                );

                TalkFactory::new()
                    ->withStartingDate($startsAt)
                    ->withEndingDate($startsAt->modify('+45 minutes'))
                    ->create()
                ;
            }
        }
    }
}
