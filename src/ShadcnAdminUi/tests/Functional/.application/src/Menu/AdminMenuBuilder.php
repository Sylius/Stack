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

namespace TestApplication\Sylius\ShadcnAdminUi\Menu;

use Knp\Menu\ItemInterface;
use Sylius\AdminUi\Knp\Menu\MenuBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'sylius_admin_ui.knp.menu_builder')]
final class AdminMenuBuilder implements MenuBuilderInterface
{
    public function __construct(
        private readonly MenuBuilderInterface $menuBuilder,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->menuBuilder->createMenu($options);

        $menu->addChild('dashboard', ['route' => 'sylius_admin_ui_dashboard'])
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'lucide:layout-dashboard')
        ;

        $library = $menu->addChild('library')
            ->setLabel('app.ui.library')
            ->setLabelAttribute('icon', 'lucide:library')
        ;
        $library->addChild('books', ['route' => 'app_admin_book_index'])->setLabel('app.ui.books');
        $library->addChild('new_book', ['route' => 'app_admin_book_create'])->setLabel('app.ui.new_book');

        $menu->addChild('section', ['extras' => ['section' => true]])
            ->setLabel('app.ui.section')
        ;
        $subMenu = $menu->addChild('submenu')
            ->setLabel('app.ui.submenu')
            ->setLabelAttribute('icon', 'lucide:menu')
        ;
        $subMenu->addChild('section_1', ['extras' => ['header' => true]])->setLabel('app.ui.section_1');
        $subMenu->addChild('item_1', ['route' => 'app_admin_book_index'])->setLabel('app.ui.item_1');

        $menu->addChild('docs', ['uri' => 'https://stack.sylius.com', 'linkAttributes' => ['target' => '_blank']])
            ->setLabel('sylius.ui.documentation')
            ->setLabelAttribute('icon', 'lucide:book-open')
        ;

        return $menu;
    }
}
