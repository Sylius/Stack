<?php

declare(strict_types=1);

namespace Sylius\BootstrapAdminUi\Twig\Extension;

use Sylius\BootstrapAdminUi\Grid\BadgeData;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for badge rendering.
 */
final class BadgeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_badge_data', [$this, 'createBadgeData']),
        ];
    }

    /**
     * Creates BadgeData from mixed input.
     * 
     * @param mixed $data The data to convert (BadgeableInterface, array, string, or null)
     * @param array $options Options containing potential overrides (labels, colors, icons)
     */
    public function createBadgeData(mixed $data, array $options = []): ?BadgeData
    {
        $overrides = [
            'labels' => $options['vars']['labels'] ?? [],
            'colors' => $options['vars']['colors'] ?? [],
            'icons' => $options['vars']['icons'] ?? [],
        ];

        return BadgeData::from($data, $overrides);
    }
}

