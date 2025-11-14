<?php

declare(strict_types=1);

namespace Sylius\BootstrapAdminUi\Grid;

/**
 * Interface for objects that can be rendered as badges in the grid.
 * 
 * Implement this interface on your enums or value objects to enable 
 * automatic badge rendering with label, color, and icon.
 */
interface BadgeableInterface
{
    /**
     * Returns the human-readable label for the badge.
     */
    public function getLabel(): string;

    /**
     * Returns the color variant for the badge (e.g., 'success', 'warning', 'danger').
     * Should match Bootstrap color variants: primary, secondary, success, danger, warning, info, light, dark.
     */
    public function getColor(): string;

    /**
     * Returns the icon to display in the badge.
     * Can be a UX Icon name (e.g., 'heroicons:check') or a simple character/emoji.
     */
    public function getIcon(): ?string;

    /**
     * Returns the value that will be used for test attributes and data attributes.
     * Typically the enum value or a unique identifier.
     */
    public function getValue(): string;
}

