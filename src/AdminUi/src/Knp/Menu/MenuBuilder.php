<?php

declare(strict_types=1);

namespace Sylius\AdminUi\Knp\Menu;

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
