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

namespace App\Repository;

use App\Entity\Talk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Talk>
 */
class TalkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Talk::class);
    }

    public function getLastTalks(int $numberOfTalks): array
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->orderBy('o.id', 'DESC')
            ->setMaxResults($numberOfTalks)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function getTotalTalks(\DatePeriod $datePeriod): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('COUNT(o.id)')
            ->andWhere(
                $queryBuilder->expr()->gte('o.startsAt', ':startDate'),
            )
            ->andWhere(
                $queryBuilder->expr()->lt('o.startsAt', ':endDate'),
            )
            ->setParameter('startDate', $datePeriod->getStartDate())
            ->setParameter('endDate', $datePeriod->getEndDate())
        ;

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function findTalkStatisticsPerDay(\DatePeriod $datePeriod): array
    {
        return $this->findTalkStatistics($datePeriod, [
            'year' => 'YEAR(o.startsAt) AS year',
            'month' => 'MONTH(o.startsAt) AS month',
            'day' => 'DAY(o.startsAt) AS day',
        ]);
    }

    public function findTalkStatisticsPerMonth(\DatePeriod $datePeriod): array
    {
        return $this->findTalkStatistics($datePeriod, [
            'year' => 'YEAR(o.startsAt) AS year',
            'month' => 'MONTH(o.startsAt) AS month',
        ]);
    }

    private function findTalkStatistics(\DatePeriod $datePeriod, array $groupBy): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('COUNT(o.id) AS total')
            ->andWhere(
                $queryBuilder->expr()->gte('o.startsAt', ':start_date'),
            )
            ->andWhere(
                $queryBuilder->expr()->lt('o.startsAt', ':end_date'),
            )
            ->setParameter('start_date', $datePeriod->getStartDate())
            ->setParameter('end_date', $datePeriod->getEndDate())
        ;

        foreach ($groupBy as $name => $select) {
            $queryBuilder
                ->addSelect($select)
                ->addGroupBy($name)
            ;
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }
}
