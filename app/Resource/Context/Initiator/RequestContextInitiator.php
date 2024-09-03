<?php

namespace App\Resource\Context\Initiator;

use App\Resource\Context\Option\OperationOption;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Context\Initiator\RequestContextInitiatorInterface;
use Sylius\Resource\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

#[AsDecorator(decorates: 'sylius.context.initiator.request_context')]
class RequestContextInitiator implements RequestContextInitiatorInterface
{
    public function __construct(
        private RequestContextInitiatorInterface $requestContextInitiator,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
    ) {
    }

    public function initializeContext(Request $request): Context
    {
        $context = $this->requestContextInitiator->initializeContext($request);

        $route = $request->attributes->get('_route');
        $syliusOptions = $request->attributes->get('_sylius');

        if (null === $syliusOptions) {
            return $context;
        }

        /** @var string|null $resource */
        $resource = $syliusOptions['resource'] ?? null;

        /** @var class-string|null $resourceClass */
        $resourceClass = $syliusOptions['resource_class'] ?? null;

        Assert::notNull($resource);
        Assert::notNull($resourceClass);

        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);

        $operation = $resourceMetadataCollection->getOperation($resource, $route);

        return $context->with(new OperationOption($operation));
    }
}
