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
}
