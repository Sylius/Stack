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

namespace Sylius\AdminUi\Knp\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\AdminUi\Knp\Menu\Event\MenuBuilderEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class MenuBuilder implements MenuBuilderInterface
{
    public const EVENT_NAME = 'sylius_admin_ui.menu.event.main';

    public function __construct(
        private readonly FactoryInterface $factory,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $root = $this->factory->createItem('root');

        $this->eventDispatcher->dispatch(
            new MenuBuilderEvent($this->factory, $root),
            self::EVENT_NAME,
        );

        return $root;
    }
}
