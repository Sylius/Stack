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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\FrontUi\Knp\Menu\AccountMenuBuilder;
use Sylius\FrontUi\Knp\Menu\AccountMenuBuilderInterface;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_front_ui.knp.menu_builder.account', AccountMenuBuilder::class)
        ->args([service('knp_menu.factory')])
        ->tag(name: 'knp_menu.menu_builder', attributes: ['method' => 'createMenu', 'alias' => 'sylius_front_ui.menu.account'])
    ;
    $services->alias(AccountMenuBuilderInterface::class, 'sylius_front_ui.knp.menu_builder.account');
};
