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

namespace Sylius\FrontUi\Knp\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class AccountMenuBuilder implements AccountMenuBuilderInterface
{
    public function __construct(private readonly FactoryInterface $factory)
    {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setLabel('sylius.ui.my_account');

        $menu
            ->addChild('dashboard', ['route' => 'sylius_front_ui_account_dashboard'])
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'home')
        ;

        $menu
            ->addChild('personal_information', ['route' => 'sylius_front_ui_account_profile_update'])
            ->setLabel('sylius.ui.personal_information')
            ->setLabelAttribute('icon', 'user')
        ;

        $menu
            ->addChild('change_password', ['route' => 'sylius_front_ui_account_change_password'])
            ->setLabel('sylius.ui.change_password')
            ->setLabelAttribute('icon', 'lock')
        ;

        return $menu;
    }
}
