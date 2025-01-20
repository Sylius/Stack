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

use Sylius\BehatBridge\Behat\Element\Admin\Action\CancelActionElement;
use Sylius\BehatBridge\Behat\Element\Admin\Action\CancelActionElementInterface;
use Sylius\BehatBridge\Behat\Element\Admin\Action\CreateActionElement;
use Sylius\BehatBridge\Behat\Element\Admin\Action\CreateActionElementInterface;
use Sylius\BehatBridge\Behat\Element\Admin\Action\UpdateActionElement;
use Sylius\BehatBridge\Behat\Element\Admin\Action\UpdateActionElementInterface;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->set('sylius_behat_bridge.behat.element.admin.action.cancel', CancelActionElement::class)
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ])
    ;
    $services->alias(CancelActionElementInterface::class, 'sylius_behat_bridge.behat.element.admin.action.cancel');

    $services
        ->set('sylius_behat_bridge.behat.element.admin.action.create', CreateActionElement::class)
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ])
    ;
    $services->alias(CreateActionElementInterface::class, 'sylius_behat_bridge.behat.element.admin.action.create');

    $services
        ->set('sylius_behat_bridge.behat.element.admin.action.update', UpdateActionElement::class)
        ->args([
            service('behat.mink.default_session'),
            service('behat.mink.parameters'),
        ])
    ;
    $services->alias(UpdateActionElementInterface::class, 'sylius_behat_bridge.behat.element.admin.action.update');
};
