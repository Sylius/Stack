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

namespace App\Twig\Component\Grid;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'sylius_grid_data_table',
    template: '@SyliusBootstrapAdminUi/shared/crud/index/content/grid/data_table.html.twig',
)]
final class DataTableComponent
{
    use DefaultActionTrait;
    use HookableComponentTrait;

    #[LiveProp(writable: true)]
    public ?string $grid = null;

    #[LiveProp(writable: true)]
    public int $page;

    /** @var array<string, mixed>|null */
    #[LiveProp(writable: true)]
    public ?array $criteria = null;

    /** @var array<string, string>|null */
    #[LiveProp(writable: true)]
    public ?array $sorting = null;

    #[LiveProp(writable: true)]
    public ?int $limit = null;

    #[LiveProp(writable: true)]
    public bool $pushOnBrowserHistory = true;

    private ?Request $request;

    public function __construct(
        #[Autowire(service: ChainProvider::class)]
        private readonly GridProviderInterface $gridProvider,
        private readonly GridViewFactoryInterface $gridViewFactory,
    ) {
    }

    public function getResources(): GridViewInterface
    {
        if (null === $this->grid) {
            throw new \RuntimeException('No Grid has been passed to the component.');
        }

        $gridDefinition = $this->gridProvider->get($this->grid);

        $config = ['page' => $this->page];

        if (null !== $this->criteria) {
            $config['criteria'] = $this->criteria;
        }

        if (null !== $this->sorting) {
            $config['sorting'] = $this->sorting;
        }

        $gridView = $this->gridViewFactory->create(
            $gridDefinition,
            new Parameters($config),
        );

        $data = $gridView->getData();

        if ($data instanceof PagerfantaInterface) {
            $data->setCurrentPage($this->page);

            if (null !== $this->limit) {
                $data->setMaxPerPage($this->limit);
            }
        }

        return $gridView;
    }

    #[LiveAction]
    public function sortBy(#[LiveArg] string $field, #[LiveArg] string $order): void
    {
        $this->sorting = [$field => $order];
    }

    #[LiveAction]
    public function updateLimit(#[LiveArg] int $limit): void
    {
        $this->page = 1;
        $this->criteria = null;
        $this->sorting = null;
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
}
