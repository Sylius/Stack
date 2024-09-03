<?php

namespace App\Twig\Context\Factory;

use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\Twig\Context\Factory\ContextFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(
    decorates: 'sylius.twig.context.factory',
)]
final class ContextFactory implements ContextFactoryInterface
{
    public function __construct(
        private readonly ContextFactoryInterface $contextFactory,
    ) {
    }

    public function create(mixed $data, Operation $operation, Context $context): array
    {
        return [
            ...$this->contextFactory->create($data, $operation, $context),
            'resource_context' => $context,
        ];
    }
}
