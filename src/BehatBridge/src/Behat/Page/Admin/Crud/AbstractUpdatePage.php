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

namespace Sylius\BehatBridge\Behat\Page\Admin\Crud;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\BehatBridge\Behat\Element\Admin\Action\CancelActionElementInterface;
use Sylius\BehatBridge\Behat\Element\Admin\Action\UpdateActionElementInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractUpdatePage extends SymfonyPage
{
    public function __construct(
        Session $session,
        \ArrayAccess $minkParameters,
        RouterInterface $router,
        private readonly CancelActionElementInterface $cancelActionElement,
        private readonly UpdateActionElementInterface $updateActionElement,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function cancel(): void
    {
        $this->cancelActionElement->cancel();
    }

    public function update(): void
    {
        $this->updateActionElement->update();
    }
}
