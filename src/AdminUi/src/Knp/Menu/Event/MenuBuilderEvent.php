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

namespace Sylius\AdminUi\Knp\Menu\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class MenuBuilderEvent extends Event
{
    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly ItemInterface $menu,
    ) {
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}
