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

namespace Tests\Sylius\ShadcnAdminUi\Functional;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PaginationMacroTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testResultsCountUsesThePluralForm(): void
    {
        self::assertSame('Affichage de 11 à 20 sur 25 éléments', $this->renderResultsCount(25, 10, 2));
    }

    public function testResultsCountUsesTheSingularForm(): void
    {
        self::assertSame('Affichage de 1 à 1 sur 1 élément', $this->renderResultsCount(1, 10, 1));
    }

    private function renderResultsCount(int $nbResults, int $maxPerPage, int $currentPage): string
    {
        $paginator = new Pagerfanta(new ArrayAdapter(range(1, $nbResults)));
        $paginator->setMaxPerPage($maxPerPage);
        $paginator->setCurrentPage($currentPage);

        return trim($this->render(<<<'TWIG'
            {% import '@SyliusShadcnAdminUi/shared/helper/pagination.html.twig' as pagination %}
            {{ pagination.results_count(paginator) }}
            TWIG, ['paginator' => $paginator]));
    }
}
