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

use Sylius\BehatBridge\Behat\Context\Hook\DoctrineORMContext;
use Sylius\BehatBridge\Behat\Context\Transform\SharedStorageContext;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->defaults()
        ->public()
    ;

    $services
        ->set('sylius_behat_bridge.behat.context.transform.shared_storage', SharedStorageContext::class)
        ->args([
            service('sylius_behat_bridge.storage.shared'),
        ])
    ;
    $services->alias(SharedStorageContext::class, 'sylius_behat_bridge.behat.context.transform.shared_storage');

    $services
        ->set('sylius_behat_bridge.behat.context.hook.doctrine.orm', DoctrineORMContext::class)
        ->args([
            service('doctrine.orm.entity_manager'),
        ])
    ;
    $services->alias(DoctrineORMContext::class, 'sylius_behat_bridge.behat.context.hook.doctrine.orm');
};
