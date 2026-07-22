# Badge Field

The badge field allows you to render colored badges with icons in your Sylius grids.

## Basic Usage

### Using with Enums (Recommended)

The cleanest way to use badges is by implementing `BadgeableInterface` on your enums:

```php
<?php

declare(strict_types=1);

namespace App\Enums;

use Sylius\BootstrapAdminUi\Grid\BadgeableInterface;

enum MyCustomStatus: string implements BadgeableInterface
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'heroicons:check',
            self::INACTIVE => 'heroicons:x-mark',
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
```

Then in your grid:

```php
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;

$gridBuilder->addField(
    TwigField::create('status', '@SyliusBootstrapAdminUi/shared/grid/field/badge.html.twig')
        ->setLabel('Status')
        ->setSortable(true)
);
```

### Using without Icons

The `getIcon()` method is required by the interface, but you can return `null` if you don't want icons:

```php
<?php

declare(strict_types=1);

namespace App\Enums;

use Sylius\BootstrapAdminUi\Grid\BadgeableInterface;

enum Priority: string implements BadgeableInterface
{
    case HIGH = 'high';
    case MEDIUM = 'medium';
    case LOW = 'low';

    public function getLabel(): string
    {
        return match ($this) {
            self::HIGH => 'High Priority',
            self::MEDIUM => 'Medium Priority',
            self::LOW => 'Low Priority',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::HIGH => 'danger',
            self::MEDIUM => 'warning',
            self::LOW => 'info',
        };
    }

    public function getIcon(): ?string
    {
        // No icons - just return null
        return null;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
```

### Using with Simple Strings

If you're displaying a simple string value:

```php
$gridBuilder->addField(
    TwigField::create('type', '@SyliusBootstrapAdminUi/shared/grid/field/badge.html.twig')
        ->setLabel('Type')
);
```

### Using with Options Override

You can override labels, colors, and icons using options:

```php
$gridBuilder->addField(
    TwigField::create('status', '@SyliusBootstrapAdminUi/shared/grid/field/badge.html.twig')
        ->setLabel('Status')
        ->withOptions([
            'vars' => [
                'labels' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ],
                'colors' => [
                    'active' => 'success',
                    'inactive' => 'secondary',
                ],
                'icons' => [
                    'active' => 'heroicons:check-circle',
                    'inactive' => 'heroicons:x-circle',
                ],
            ],
        ])
);
```

## BadgeableInterface

The `BadgeableInterface` provides a contract for objects that can be displayed as badges:

```php
interface BadgeableInterface
{
    /**
     * Returns the human-readable label for the badge.
     */
    public function getLabel(): string;

    /**
     * Returns the color variant for the badge.
     * Should be one of: primary, secondary, success, danger, warning, info, light, dark
     */
    public function getColor(): string;

    /**
     * Returns the icon to display in the badge (optional).
     * Return null if you don't want an icon.
     * Can be a UX Icon name (e.g., 'heroicons:check') or a simple character/emoji.
     */
    public function getIcon(): ?string;

    /**
     * Returns the value for test attributes and data attributes.
     */
    public function getValue(): string;
}
```

## Colors

Available Bootstrap color variants:
- `primary` - Blue
- `secondary` - Gray
- `success` - Green
- `danger` - Red
- `warning` - Yellow/Orange
- `info` - Light blue
- `light` - Light gray
- `dark` - Dark gray/black

## Icons

Icons are **optional**. The `getIcon()` method returns `?string` (nullable).

### No Icons
Simply return `null` if you don't want icons:

```php
public function getIcon(): ?string
{
    return null; // No icon will be displayed
}
```

### With Icons
Icons can be:
1. **UX Icons** (recommended): Use the format `bundle:icon-name`, e.g., `heroicons:check`
2. **Simple characters/emojis**: e.g., `✓`, `⚠`, `🔥`

Popular UX icon bundles:
- `heroicons:` - Heroicons
- `fa:` - Font Awesome
- `bi:` - Bootstrap Icons
- `lucide:` - Lucide Icons

### Selective Icons
You can also have icons for some cases only:

```php
public function getIcon(): ?string
{
    return match ($this) {
        self::CRITICAL => '🔥',      // Icon for critical
        self::ERROR => '✗',          // Icon for error
        default => null,             // No icon for other cases
    };
}
```

## Architecture

The badge system consists of:

1. **`BadgeableInterface`**: Contract for badge-displayable objects
2. **`BadgeData`**: Value object that normalizes badge data from various sources
3. **`BadgeExtension`**: Twig extension that provides the `sylius_badge_data()` function
4. **Badge templates**: Twig macros for rendering badges

This architecture ensures:
- **Type safety**: No magic method calls via Twig's `is defined`
- **Separation of concerns**: Logic in PHP, not Twig
- **Flexibility**: Supports enums, arrays, and strings
- **Testability**: Value objects and interfaces are easy to test

## Testing

Test attributes are automatically added to badges for E2E testing:

```html
<span class="badge rounded-pill text-success" data-test="badge-healthy">
    ✓ Healthy
</span>
```

The test attribute uses the `getValue()` method (for `BadgeableInterface` implementations) or the label as a fallback.

