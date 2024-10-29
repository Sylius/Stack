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

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\FrontUi\Symfony\EventListener\Attribute;

use Sylius\FrontUi\AccountEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class AsChangePasswordListener extends AsEventListener
{
    public function __construct(
        ?string $method = null,
        int $priority = 0,
        ?string $dispatcher = null,
    ) {
        parent::__construct($this->buildEventName(), $method, $priority, $dispatcher);
    }

    private function buildEventName(): string
    {
        return AccountEvents::CHANGE_PASSWORD;
    }
}
