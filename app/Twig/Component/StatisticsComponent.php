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

namespace App\Twig\Component;

use App\Statistics\Provider\StatisticsProvider;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(
    template: 'component/statistics.html.twig',
)]
final class StatisticsComponent
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    public function __construct(
        private readonly StatisticsProvider $statisticsProvider,
    ) {
    }

    #[LiveProp]
    public string $startDate = 'first day of january this year';

    #[LiveProp]
    public string $endDate = 'first day of january next year';

    #[LiveProp]
    public string $period = 'year';

    #[LiveProp]
    public string $interval = 'month';

    #[ExposeInTemplate(name: 'statistics')]
    public function getStatistics(): array
    {
        $statistics = $this->statisticsProvider->provide(
            $this->interval,
            new \DatePeriod(
                new \DateTimeImmutable($this->startDate),
                $this->resolveInterval(),
                new \DateTimeImmutable($this->endDate),
            ),
        );

        $talksSummary = [
            'intervals' => array_column($statistics->talks ?? [], 'period'),
            'talks' => array_map(
                static fn (int $total): string => (string) $total,
                array_column($statistics->talks ?? [['total' => 2]], 'total'),
            ),
        ];

        return [
            'business_activity_summary' => $statistics->businessActivitySummary,
            'talks_summary' => $talksSummary,
        ];
    }

    #[LiveAction]
    public function changeRange(
        #[LiveArg]
        string $period,
        #[LiveArg]
        string $interval,
    ): void {
        $this->period = $period;
        $this->interval = $interval;

        $this->resolveDates();
    }

    #[LiveAction]
    public function getPreviousPeriod(): void
    {
        $this->startDate = (new \DateTimeImmutable($this->startDate))->sub(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
        $this->endDate = (new \DateTimeImmutable($this->endDate))->sub(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
    }

    #[LiveAction]
    public function getNextPeriod(): void
    {
        $this->startDate = (new \DateTimeImmutable($this->startDate))->add(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
        $this->endDate = (new \DateTimeImmutable($this->endDate))->add(new \DateInterval($this->resolveChangePeriodInterval()))->format('Y-m-d');
    }

    private function resolveInterval(): \DateInterval
    {
        $interval = match ($this->interval) {
            'day' => 'P1D',
            'month' => 'P1M',
            default => throw new \InvalidArgumentException(sprintf('Interval "%s" is not supported.', $this->interval)),
        };

        return new \DateInterval($interval);
    }

    private function resolveDates(): void
    {
        [$startDate, $endDate] = match ($this->period) {
            'year' => [
                (new \DateTimeImmutable('first day of January this year'))->format('Y-m-d'),
                (new \DateTimeImmutable('first day of January next year'))->format('Y-m-d'),
            ],
            'month' => [
                (new \DateTimeImmutable('first day of this month'))->format('Y-m-d'),
                (new \DateTimeImmutable('first day of next month'))->format('Y-m-d'),
            ],
            '2 weeks' => [
                (new \DateTimeImmutable('monday previous week'))->format('Y-m-d'),
                (new \DateTimeImmutable('monday next week'))->format('Y-m-d'),
            ],
            default => throw new \InvalidArgumentException(sprintf('Period "%s" is not supported.', $this->period)),
        };

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    private function resolveChangePeriodInterval(): string
    {
        return match ($this->period) {
            'year' => 'P1Y',
            'month' => 'P1M',
            '2 weeks' => 'P2W',
            default => throw new \InvalidArgumentException(sprintf('Period "%s" is not supported.', $this->period)),
        };
    }
}
