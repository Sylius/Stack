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

use App\Repository\ConferenceRepository;
use App\Repository\SpeakerRepository;
use App\Repository\TalkRepository;
use App\Statistics\ValueObject\BusinessActivitySummary;
use App\Statistics\ValueObject\Statistics;
use Webmozart\Assert\Assert;

final class StatisticsProvider
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly ConferenceRepository $conferenceRepository,
        private readonly DayTalksProvider $dayTalksProvider,
        private readonly MonthTalksProvider $monthTalksProvider,
    ) {
    }

    public function provide(
        string $intervalType,
        \DatePeriod $datePeriod,
    ): Statistics {
        $format = $this->getPeriodFormat($intervalType);

        $talkStatistics = match ($intervalType) {
            'day' => $this->dayTalksProvider->provide($datePeriod),
            'month' => $this->monthTalksProvider->provide($datePeriod),
            default => throw new \RuntimeException(sprintf('Getting talks statistics for this "%s" period type is not supported.', $intervalType))
        };

        return new Statistics(
            talks: $this->withFormattedDates($talkStatistics, $format),
            businessActivitySummary: new BusinessActivitySummary(
                totalTalks: $this->talkRepository->findTotalTalks($datePeriod),
                totalSpeakers: $this->speakerRepository->findTotalTalks($datePeriod),
                totalConferences: $this->conferenceRepository->findTotalConferences($datePeriod),
            ),
        );
    }

    /**
     * @param array<array{total: int, period: \DateTimeInterface}> $sales
     *
     * @return array<array{total: int, period: string}>
     */
    private function withFormattedDates(array $sales, string $format): array
    {
        return array_map(fn (array $entry) => [
            'period' => $entry['period']->format($format),
            'total' => $entry['total'],
        ], $sales);
    }

    private function getPeriodFormat(string $intervalType): string
    {
        $formatsMap = [
            'day' => 'Y-m-d',
            'month' => 'M Y',
        ];

        Assert::keyExists($formatsMap, $intervalType);

        return $formatsMap[$intervalType];
    }
}
