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

namespace Sylius\ShadcnAdminUi\Twig\Extension;

use Twig\Extra\Html\HtmlAttr\AttributeValueInterface;
use Twig\Extra\Html\HtmlAttr\MergeableInterface;

/**
 * A space-separated list that html_attr() can merge and that ComponentAttributes can cast to a string.
 */
final class SpaceSeparatedTokens implements AttributeValueInterface, MergeableInterface, \Stringable
{
    /** @var list<string> */
    private readonly array $tokens;

    /**
     * @param iterable<mixed>|string $value
     */
    public function __construct(iterable|string $value)
    {
        $this->tokens = self::tokens($value);
    }

    public function mergeInto(mixed $previous): mixed
    {
        return new self([...self::tokens($previous), ...$this->tokens]);
    }

    public function appendFrom(mixed $newValue): mixed
    {
        return new self([...$this->tokens, ...self::tokens($newValue)]);
    }

    public function getValue(): ?string
    {
        return [] === $this->tokens ? null : implode(' ', $this->tokens);
    }

    public function __toString(): string
    {
        return $this->getValue() ?? '';
    }

    /**
     * @return list<string>
     */
    private static function tokens(mixed $value): array
    {
        if ($value instanceof self) {
            return $value->tokens;
        }

        $tokens = is_iterable($value) ? [...$value] : [$value];

        return array_values(array_map('strval', array_filter($tokens, static fn (mixed $token): bool => null !== $token && false !== $token)));
    }
}
