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

namespace Sylius\BehatBridge\Behat\Element\Admin\Action;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class CreateActionElement extends Element implements CreateActionElementInterface
{
    public function create(): void
    {
        $this->getElement('create_button')->click();
    }

    protected function getDefinedElements(): array
    {
        return [
            'create_button' => '[type=submit]:contains("Create")',
        ];
    }
}
