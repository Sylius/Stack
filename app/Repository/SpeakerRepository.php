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

use App\Entity\Speaker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Speaker>
 */
class SpeakerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Speaker::class);
    }

    public function getLastSpeakers(int $numberOfSpeakers): array
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->orderBy('o.id', 'DESC')
            ->setMaxResults($numberOfSpeakers)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function findTotalTalks(\DatePeriod $datePeriod): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('COUNT(DISTINCT o.id)')
            ->join('o.talks', 't')
            ->andWhere(
                $queryBuilder->expr()->gte('t.startsAt', ':start_date'),
            )
            ->andWhere(
                $queryBuilder->expr()->lt('t.startsAt', ':end_date'),
            )
            ->setParameter('start_date', $datePeriod->getStartDate())
            ->setParameter('end_date', $datePeriod->getEndDate())
        ;

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
