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
            ->addChild('dashboard', [
                'route' => 'sylius_admin_ui_dashboard',
            ])
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'tabler:dashboard')
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
            ->setLabelAttribute('icon', 'tabler:books')
        ;

        $library->addChild('books', ['route' => 'app_admin_book_index'])
            ->setLabel('app.ui.books')
            ->setLabelAttribute('icon', 'book')
        ;
    }

    private function addConfigurationSubMenu(ItemInterface $menu): void
    {
        $configuration = $menu
            ->addChild('configuration')
            ->setLabel('app.ui.configuration')
            ->setLabelAttribute('icon', 'tabler:dashboard')
            ->setExtra('always_open', true)
        ;

        $configuration->addChild('conferences', ['route' => 'app_admin_conference_index'])
            ->setLabel('app.ui.conferences')
        ;

        $configuration->addChild('talks', ['route' => 'app_admin_talk_index'])
            ->setLabel('app.ui.talks')
        ;

        $configuration->addChild('speakers', ['route' => 'app_admin_speaker_index'])
            ->setLabel('app.ui.speakers')
        ;
    }
}
