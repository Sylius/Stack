<?php

namespace App\Twig\Component\Grid;

use App\Entity\Book;
use App\Resource\Context\Option\OperationOption;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\ChainProvider;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Resource\Context\Initiator\RequestContextInitiatorInterface;
use Sylius\Resource\Grid\View\Factory\GridViewFactoryInterface;
use Sylius\Resource\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'sylius_grid', template: 'components/grid.html.twig', csrf: false)]
class GridComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $operationName;

    #[LiveProp(writable: true)]
    public string $grid;

    #[LiveProp(writable: true)]
    public int $page = 1;

    public array $currentCriteria = [];
    #[LiveProp(writable: true)]
    public int $nbPages;

    public ?GridViewInterface $resources = null;

    public function __construct(
        private DataProviderInterface $dataProvider,
        #[Autowire(service: ChainProvider::class)]
        private GridProviderInterface $gridProvider,
        private GridViewFactoryInterface $gridViewFactory,
        private RequestContextInitiatorInterface $requestContextInitiator,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private RequestStack $requestStack,
        protected FormFactoryInterface $formFactory,
    )
    {
    }

    public function getResources(): GridViewInterface
    {
        if (null !== $this->resources) {
            return $this->resources;
        }

        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create(Book::class);
        $operation = $resourceMetadataCollection->getOperation('app.book', $this->operationName);

        $gridDefinition = $this->gridProvider->get($this->grid);

        $context = $this->requestContextInitiator->initializeContext($this->requestStack->getCurrentRequest() ?? new Request());
        $context = $context->with(new OperationOption($operation));
        //dd($context);

        return $this->gridViewFactory->create($gridDefinition, $context, new Parameters(['page' => $this->page, 'criteria' => $this->currentCriteria, 'resource_metadata' => $operation->getResource()]), []);
    }

    #[LiveAction]
    public function changePage(#[LiveArg] int $page): void
    {
        $this->page = $page;
    }

    #[LiveAction]
    public function previousPage(): void
    {
        if ($this->page <= 1) {
            return;
        }

        --$this->page;
    }

    #[LiveAction]
    public function nextPage(): void
    {
        if ($this->page >= $this->nbPages) {
            return;
        }

        ++$this->page;
    }
}
