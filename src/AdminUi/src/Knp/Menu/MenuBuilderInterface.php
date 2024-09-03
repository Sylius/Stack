<?php

declare(strict_types=1);

namespace Sylius\AdminUi\Knp\Menu;

use Knp\Menu\ItemInterface;

interface MenuBuilderInterface
{
    public function createMenu(array $options): ItemInterface;
}
