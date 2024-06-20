<?php

/*
 * This file is part of SyliusUi.
 *
 * (c) Monofony
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\AdminUi\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class MenuBuilder implements MenuBuilderInterface
{
    public function __construct(private readonly FactoryInterface $factory)
    {
    }

    public function createMenu(array $options): ItemInterface
    {
        return $this->factory->createItem('root');
    }
}
