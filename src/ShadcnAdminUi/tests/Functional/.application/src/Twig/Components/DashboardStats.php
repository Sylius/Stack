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

namespace TestApplication\Sylius\ShadcnAdminUi\Twig\Components;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use TestApplication\Sylius\ShadcnAdminUi\Entity\Book;

/**
 * Customization example: shadcn statistics cards added to the dashboard
 * through a hook (see config/packages/sylius_twig_hooks.yaml).
 */
#[AsTwigComponent]
final class DashboardStats
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getTotal(): int
    {
        return $this->em->getRepository(Book::class)->count([]);
    }

    public function getAvailable(): int
    {
        return $this->em->getRepository(Book::class)->count(['available' => true]);
    }

    /**
     * @return list<array{category: ?\App\Enum\BookCategory, total: int}>
     */
    public function getByCategory(): array
    {
        return $this->em->createQueryBuilder()
            ->select('b.category AS category, COUNT(b.id) AS total')
            ->from(Book::class, 'b')
            ->groupBy('b.category')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return list<Book>
     */
    public function getLatest(): array
    {
        return $this->em->getRepository(Book::class)->findBy([], ['createdAt' => 'DESC'], 5);
    }
}
