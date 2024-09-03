<?php

namespace App\Resource\Context\Option;

use Sylius\Resource\Metadata\Operation;

final class OperationOption
{
    public function __construct(
        private readonly Operation $operation,
    ) {
    }

    public function operation(): Operation
    {
        return $this->operation;
    }
}
