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

final class StatisticsProvider
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly COnferenceRepository $conferenceRepository,
    ) {
    }

    public function provide(
        string $intervalType,
        \DatePeriod $datePeriod,
    ): Statistics {
        return new Statistics(
            businessActivitySummary: new BusinessActivitySummary(
                totalTalks: $this->talkRepository->findTotalTalks($datePeriod),
                totalSpeakers: $this->speakerRepository->findTotalTalks($datePeriod),
                totalConferences: $this->conferenceRepository->findTotalConferences($datePeriod),
            ),
        );
    }
}
