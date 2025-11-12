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

namespace Sylius\TwigComponentGrid\Twig\Component;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

/**
 * @experimental
 */
trait ComponentWithDataTableTrait
{
    use ComponentToolsTrait;

    #[LiveProp(writable: true, url: true)]
    public int $page = 1;

    /** @var array<string, mixed>|null */
    #[LiveProp(writable: true)]
    public array|null $criteria;

    /** @var array<string, string>|null */
    #[LiveProp(writable: true)]
    public array|null $sorting = null;

    #[LiveProp(writable: true)]
    public int|null $limit = null;

    #[LiveProp(writable: true)]
    public array|null $columns = null;

    abstract protected function getGrid(): ?string;

    private GridViewFactoryInterface $gridViewFactory;

    private GridProviderInterface $gridProvider;

    public function getResources(): GridViewInterface
    {
        $gridDefinition = $this->gridProvider->get($this->getGrid());

        $config = [
            'page' => $this->page,
        ];

        if (null !== $this->criteria) {
            $config['criteria'] = $this->criteria;
        }

        if (null !== $this->sorting) {
            $config['sorting'] = $this->sorting;
        }

        if (null !== $this->limit) {
            $config['limit'] = $this->limit;
        }

        $gridView = $this->gridViewFactory->create(
            $gridDefinition,
            new Parameters($config),
        );

        $data = $gridView->getData();

        if ($data instanceof PagerfantaInterface) {
            if (null !== $this->limit) {
                $data->setMaxPerPage($this->limit);
            }

            $data->setCurrentPage($this->page);

            $this->emit('gridLoaded', [
                'nbResults' => $data->getNbResults(),
            ]);
        }

        return $gridView;
    }

    #[LiveAction]
    public function sortBy(#[LiveArg] string $field, #[LiveArg] string $order): void
    {
        $this->sorting = [
            $field => $order,
        ];
    }

    #[LiveAction]
    public function updateLimit(#[LiveArg] int $limit): void
    {
        $this->page = 1;
        $this->limit = $limit;
    }

    #[LiveAction]
    public function changePage(#[LiveArg] int $page): void
    {
        $this->page = $page;
    }

    #[LiveAction]
    public function previousPage(): void
    {
        --$this->page;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        ++$this->page;
    }

    /** @param array<string, mixed> $criteria */
    #[LiveListener('criteriaSubmitted')]
    public function onCriteriaSubmitted(#[LiveArg] array $criteria): void
    {
        $this->criteria = array_merge($this->criteria ?? [], $criteria);
    }

    /**
     * @internal
     */
    #[Required]
    public function setGridProvider(
        #[Autowire(service: ChainProvider::class)]
        GridProviderInterface $gridProvider,
    ): void {
        $this->gridProvider = $gridProvider;
    }

    /**
     * @internal
     */
    #[Required]
    public function setGridViewFactory(GridViewFactoryInterface $gridViewFactory): void
    {
        $this->gridViewFactory = $gridViewFactory;
    }
}
