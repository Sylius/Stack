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

namespace Sylius\TwigHooks\Hookable\Metadata;

use Sylius\TwigHooks\Bag\DataBagInterface;
use Sylius\TwigHooks\Bag\ScalarDataBagInterface;
use Sylius\TwigHooks\Hook\Metadata\HookMetadata;

class HookableMetadata
{
    /**
     * @param array<string> $prefixes
     */
    public function __construct(
        public readonly HookMetadata $renderedBy,
        public readonly DataBagInterface $context,
        public readonly ScalarDataBagInterface $configuration,
        public readonly array $prefixes = [],
    ) {
        foreach ($prefixes as $prefix) {
            if (!is_string($prefix)) {
                throw new \InvalidArgumentException('Parent name must be a string.');
            }
        }
    }

    public function hasPrefixes(): bool
    {
        return count($this->prefixes) > 0;
    }

    public function withConfiguration(ScalarDataBagInterface $configuration): self
    {
        return new self($this->renderedBy, $this->context, $configuration, $this->prefixes);
    }

    public function withContext(DataBagInterface $context): self
    {
        return new self($this->renderedBy, $context, $this->configuration, $this->prefixes);
    }
}
