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

namespace TestApplication\Sylius\TwigExtra\State\Provider;

use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ProviderInterface;
use TestApplication\Sylius\TwigExtra\Resource\BookResource;

class BookItemProvider implements ProviderInterface
{
    public function provide(Operation $operation, Context $context): BookResource
    {
        return new BookResource('shinning', 'Shinning');
    }
}
