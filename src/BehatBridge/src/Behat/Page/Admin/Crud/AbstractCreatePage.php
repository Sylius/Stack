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
use Sylius\BehatBridge\Behat\Element\Admin\Action\CreateActionElementInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractCreatePage extends SymfonyPage
{
    public function __construct(
        Session $session,
        \ArrayAccess $minkParameters,
        RouterInterface $router,
        private readonly CancelActionElementInterface $cancelActionElement,
        private readonly CreateActionElementInterface $createActionElement,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function cancel(): void
    {
        $this->cancelActionElement->cancel();
    }

    public function create(): void
    {
        $this->createActionElement->create();
    }
}
