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

namespace Tests\Sylius\AdminUi\Unit\Knp\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\TestCase;
use Sylius\AdminUi\Knp\Menu\Event\MenuBuilderEvent;
use Sylius\AdminUi\Knp\Menu\MenuBuilder;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class MenuBuilderTest extends TestCase
{
    public function testItCreatesMenuRootAndThrowsEvent(): void
    {
        $factory = $this->createMock(FactoryInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $menuBuilder = new MenuBuilder($factory, $eventDispatcher);

        $root = new MenuItem('root', $factory);

        $factory
            ->expects($this->once())
            ->method('createItem')
            ->with('root')
            ->willReturn($root)
        ;

        $event = new MenuBuilderEvent($factory, $root);
        $eventDispatcher->expects($this->once())->method('dispatch')->with($event, 'sylius_admin_ui.menu.event.main');

        self::assertSame($root, $menuBuilder->createMenu([]));
    }
}
