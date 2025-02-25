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

namespace App\Statistics\Provider;

use App\Repository\TalkRepository;

final class MonthTalksProvider
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
    ) {
    }

    /**
     * @return array<array{period: \DateTimeInterface, total: int}>
     */
    public function provide(\DatePeriod $datePeriod): array
    {
        $talkStatistics = $this->talkRepository->findTalkStatisticsPerMonth($datePeriod);

        $talks = [];
        foreach ($datePeriod as $date) {
            $talks[] = [
                'period' => $date,
                'total' => $this->getTotalForDate($talkStatistics, $date),
            ];
        }

        return $talks;
    }

    /** @param array<array{total: string|int, year: int, month: int}> $totals */
    private function getTotalForDate(array $totals, \DateTimeInterface $date): int
    {
        $formattedPeriodDate = $date->format('Y-n');

        foreach ($totals as $entry) {
            $entryDate = $entry['year'] . '-' . $entry['month'];
            if ($formattedPeriodDate === $entryDate) {
                return (int) $entry['total'];
            }
        }

        return 0;
    }
}
