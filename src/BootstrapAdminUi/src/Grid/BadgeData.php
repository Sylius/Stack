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

namespace Sylius\BootstrapAdminUi\Grid;

/**
 * Value object representing badge display data.
 *
 * This class normalizes badge data from various sources (BadgeableInterface, arrays, strings)
 * into a consistent format for rendering.
 */
final readonly class BadgeData
{
    public function __construct(
        public string $label,
        public string $color,
        public ?string $icon = null,
        public ?string $value = null,
    ) {
    }

    /**
     * Creates BadgeData from a BadgeableInterface implementation.
     */
    public static function fromBadgeable(BadgeableInterface $badgeable): self
    {
        return new self(
            label: $badgeable->getLabel(),
            color: $badgeable->getColor(),
            icon: $badgeable->getIcon(),
            value: $badgeable->getValue(),
        );
    }

    /**
     * Creates BadgeData from an array with optional overrides.
     *
     * @param array{label?: string, color?: string, icon?: string, value?: string} $data
     * @param array{labels?: array<string, string>, colors?: array<string, string>, icons?: array<string, string>} $overrides
     */
    public static function fromArray(array $data, array $overrides = []): self
    {
        $value = $data['value'] ?? $data['label'] ?? 'unknown';

        return new self(
            label: $overrides['labels'][$value] ?? $data['label'] ?? $value,
            color: $overrides['colors'][$value] ?? $data['color'] ?? 'primary',
            icon: $overrides['icons'][$value] ?? $data['icon'] ?? null,
            value: $value,
        );
    }

    /**
     * Creates BadgeData from a simple string value.
     */
    public static function fromString(string $value, array $overrides = []): self
    {
        return new self(
            label: $overrides['labels'][$value] ?? $value,
            color: $overrides['colors'][$value] ?? 'grey',
            icon: $overrides['icons'][$value] ?? null,
            value: $value,
        );
    }

    /**
     * Creates BadgeData from mixed input (auto-detection).
     *
     * @param BadgeableInterface|array|string|null $data
     * @param array{labels?: array<string, string>, colors?: array<string, string>, icons?: array<string, string>} $overrides
     */
    public static function from(mixed $data, array $overrides = []): ?self
    {
        if ($data === null) {
            return null;
        }

        if ($data instanceof BadgeableInterface) {
            return self::fromBadgeable($data);
        }

        if (is_array($data)) {
            return self::fromArray($data, $overrides);
        }

        if (is_string($data)) {
            return self::fromString($data, $overrides);
        }

        // Fallback: convert to string
        return self::fromString((string) $data, $overrides);
    }
}
