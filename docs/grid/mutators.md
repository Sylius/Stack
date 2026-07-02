# Mutators

When using packages that provide built-in grids (such as Sylius E-Commerce or Sylius plugins), you may need to customize their configuration.

Grid mutators allow you to modify an existing grid without overriding its original definition.

## Usage

Let's assume the `app_book` grid already contains a `title` field.
We want the grid to be sorted by title.

To achieve this, create the following grid mutator:

```php
<?php

namespace App\Grid\Mutator;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGridMutator;
use Sylius\Component\Grid\Mutator\GridMutatorInterface;

#[AsGridMutator(grid: 'app_book')]
final class SortByTitleBookGridMutator implements GridMutatorInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder->orderBy('title', 'asc');
    }
}
```

## Priorities

If multiple mutators target the same grid, you can control the order in which they are executed by using the `priority` option.

Mutators with a higher priority are executed before those with a lower priority.

```diff
<?php

namespace App\Grid\Mutator;

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGridMutator;
use Sylius\Component\Grid\Mutator\GridMutatorInterface;

#[AsGridMutator(
    grid: 'app_book',
+    priority: 20,
)]
final class SortByTitleBookGridMutator implements GridMutatorInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder->orderBy('title', 'asc');
    }
}
```
