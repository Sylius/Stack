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

namespace Sylius\TwigHooks\Bag;

class DataBag implements DataBagInterface
{
    /**
     * @param array<string, mixed> $container
     */
    public function __construct(private array $container = [])
    {
        foreach ($container as $key => $value) {
            if (!is_string($key)) {
                throw new \InvalidArgumentException('The key must be a string.');
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->container);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->container[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('The offset must be a string.');
        }

        if ('' === $offset) {
            throw new \InvalidArgumentException('The offset must not be an empty string.');
        }

        $this->container[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->container);
    }

    public function __get(string $name): mixed
    {
        return $this->container[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return $this->offsetExists($name);
    }

    public function all(): array
    {
        return $this->container;
    }
}
