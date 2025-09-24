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

namespace Sylius\AdminUi\Twig\Extension;

use Sylius\AdminUi\Context\Context;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class ContextExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly Context $context,
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'sylius_admin_ui_context' => $this->context,
        ];
    }
}
