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
}
