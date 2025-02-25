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

use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conference>
 */
class ConferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conference::class);
    }

    public function findTotalConferences(\DatePeriod $datePeriod): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->select('COUNT(o.id)')
            ->andWhere(
                $queryBuilder->expr()->gte('o.startsAt', ':start_date'),
            )
            ->andWhere(
                $queryBuilder->expr()->lt('o.startsAt', ':end_date'),
            )
            ->setParameter('start_date', $datePeriod->getStartDate())
            ->setParameter('end_date', $datePeriod->getEndDate())
        ;

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
