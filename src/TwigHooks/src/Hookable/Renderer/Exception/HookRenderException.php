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

namespace Sylius\TwigHooks\Hookable\Renderer\Exception;

use Twig\Error\Error;
use Twig\Error\RuntimeError;
use Twig\Source;

class HookRenderException extends RuntimeError
{
    public function __construct(string $message, ?int $lineno = null, ?Source $source = null, ?\Throwable $previous = null)
    {
        $lineno ??= $previous?->getLine() ?? -1;
        $source ??= $previous instanceof Error ? $previous->getSourceContext() : null;

        parent::__construct(
            $message,
            $lineno,
            $source,
            $previous,
        );
    }
}
