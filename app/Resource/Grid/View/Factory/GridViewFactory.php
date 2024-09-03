<?php

namespace App\Resource\Grid\View\Factory;

use App\Resource\Context\Option\OperationOption;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Grid\View\Factory\GridViewFactoryInterface;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Symfony\Routing\Factory\RouteName\OperationRouteNameFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'sylius.grid.view_factory.legacy')]
class GridViewFactory implements GridViewFactoryInterface
{
    public function __construct(
        private readonly GridViewFactoryInterface $gridViewFactory,
        private readonly OperationRouteNameFactoryInterface $routeNameFactory,
    ) {
    }

    public function create(Grid $grid, Context $context, Parameters $parameters, array $driverConfiguration): GridView
    {
        $resource = $context->get(OperationOption::class)
            ?->operation()
            ->getResource() ?? null;

        if (null === $resource) {
            return $this->gridViewFactory->create($grid, $context, $parameters, $driverConfiguration);
        }

        $operations = $resource->getOperations() ?? new Operations();

        $operationsMap = [];

        /** @var Operation $operation */
        foreach ($operations as $operation) {
            // The operation is not populated with resource yet
            $operation = $operation->withResource($resource);
            $shortName = $operation->getShortName();

            $operationsMap[$operation->getShortName()] = $this->routeNameFactory->createRouteName($operation, $shortName);
        }

        //dd($routes);

        $parameters = new Parameters([...$parameters->all(), 'operations' => $operationsMap]);

        return $this->gridViewFactory->create($grid, $context, $parameters, $driverConfiguration);
    }
}
