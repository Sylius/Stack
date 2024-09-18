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

namespace TestApplication\Sylius\TwigExtra\Grid\Provider;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use TestApplication\Sylius\TwigExtra\Resource\BookResource;

class BookGridProvider implements DataProviderInterface
{
    public function getData(Grid $grid, Parameters $parameters): Pagerfanta
    {
        return new Pagerfanta(new ArrayAdapter([
            new BookResource('shinning', 'Shinning'),
            new BookResource('carrie', 'Carrie'),
        ]));
    }
}
