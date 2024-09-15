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

namespace App\Menu;

use Knp\Menu\ItemInterface;
use Sylius\AdminUi\Knp\Menu\MenuBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'sylius_admin_ui.knp.menu_builder')]
final class AdminMenuBuilder implements MenuBuilderInterface
{
    public function __construct(private MenuBuilderInterface $menuBuilder)
    {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->menuBuilder->createMenu($options);

        $menu
            ->addChild('dashboard')
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'dashboard')
        ;

        $this->addLibrarySubMenu($menu);
        $this->addConfigurationSubMenu($menu);

        return $menu;
    }

    private function addLibrarySubMenu(ItemInterface $menu): void
    {
        $library = $menu
            ->addChild('library')
            ->setLabel('app.ui.library')
            ->setLabelAttribute('icon', 'users')
        ;

        $library->addChild('books', ['route' => 'app_admin_book_index'])
            ->setLabel('app.ui.books')
            ->setLabelAttribute('icon', 'book')
        ;

        $library->addChild('authors')
            ->setLabel('Authors')
            ->setLabelAttribute('icon', 'folder')
        ;
    }

    private function addConfigurationSubMenu(ItemInterface $menu): void
    {
        $library = $menu
            ->addChild('configuration')
            ->setLabel('Configuration')
            ->setLabelAttribute('icon', 'dashboard')
        ;

        $library->addChild('channels')
            ->setLabel('Channels')
            ->setLabelAttribute('icon', 'shuffle');

        $library->addChild('countries')
            ->setLabel('Countries')
            ->setLabelAttribute('icon', 'flag');

        $library->addChild('zones')
            ->setLabel('Zones')
            ->setLabelAttribute('icon', 'globe');

        $library->addChild('administrators')
            ->setLabel('Admin Users')
            ->setLabelAttribute('icon', 'lock');
    }
}
