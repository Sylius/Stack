# Actions

## Creating a custom Action

There are certain cases when built-in action types are not enough.

All you need to do is create your own action template and register it
for the `sylius_grid`.

In this example, we will specify the action button's icon to be `mail` and its
colour to be `purple` inside the template.

{% code title="@App/Grid/Action/contactSupplier.html.twig" lineNumbers="true" %}
```twig
{% import '@SyliusUi/Macro/buttons.html.twig' as buttons %}

{% set path = options.link.url|default(path(options.link.route, options.link.parameters)) %}

{{ buttons.default(path, action.label, null, 'mail', 'purple') }}
```
{% endcode %}

Now configure the new action's template like below in
`config/packages/sylius_grid.yaml`:

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    templates:
        action:
            contactSupplier: "@App/Grid/Action/contactSupplier.html.twig"
```
{% endcode %}

From now on, you can use your new action type in the grid configuration!

Let's assume that you already have a route for contacting your
suppliers, then you can configure the grid action:

{% tabs %}
{% tab title="YAML" %}
{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_admin_supplier:
            driver:
                name: doctrine/orm
                options:
                    class: App\Entity\Supplier
            actions:
                item:
                    contactSupplier:
                        type: contactSupplier
                        label: Contact Supplier
                        options:
                            link:
                                route: app_admin_contact_supplier
                                parameters:
                                    id: resource.id
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use App\Entity\Supplier;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_admin_supplier', Supplier::class)
        ->addActionGroup(
            ItemActionGroup::create(
                Action::create('contactSupplier', 'contactSupplier')
                    ->setLabel('Contact Supplier')
                    ->setOptions([
                        'link' => [
                            'route' => 'app_admin_contact_supplier',
                            'parameters' => [
                                'id' => 'resource.id',
                            ],
                        ],
                    ])
            )
        ])
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/AdminSupplierGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\Supplier;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AdminSupplierGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_admin_supplier';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('contactSupplier', 'contactSupplier')
                        ->setLabel('Contact Supplier')
                        ->setOptions([
                            'link' => [
                                'route' => 'app_admin_contact_supplier',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                        ])
                )
            ])
        ;    
    }
    
    public function getResourceClass(): string
    {
        return Supplier::class;
    }
}
```
{% endcode %}
{% endtab %}
{% endtabs %}

## Creating a custom Bulk Action

In some cases, forcing the user to click a button for each item in a grid isn't practical.
Fortunately, you can take advantage of built-in bulk actions. However, these may not always be sufficient and might need customization.

To do this, simply create your own bulk action template and register it inside the `sylius_grid`.

In the template we will specify the button's icon to be `export` and its
colour to be `orange`.

{% code title="@App/Grid/BulkAction/export.html.twig" %}
```twig
{% import '@SyliusUi/Macro/buttons.html.twig' as buttons %}

{% set path = options.link.url|default(path(options.link.route)) %}

{{ buttons.default(path, action.label, null, 'export', 'orange') }}
```
{% endcode %}

Now configure the new action's template:

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    templates:
        bulk_action:
            export: "@App/Grid/BulkAction/export.html.twig"
```
{% endcode %}

From now on, you can use your new bulk action type in the grid configuration!

Let's assume that you already have a route for exporting by injecting
ids. Now, you can configure the grid action:

{% tabs %}
{% tab title="YAML" %}
{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_admin_product:
            ...
            actions:
                bulk:
                    export:
                        type: export
                        label: Export Data
                        options:
                            link:
                                route: app_admin_product_export
                                parameters:
                                    format: csv
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use App\Entity\Product;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_admin_product', Product::class)
        ->addActionGroup(
            BulkActionGroup::create(
                Action::create('export', 'export')
                    ->setLabel('Export Data')
                    ->setOptions([
                        'link' => [
                            'route' => 'app_admin_product_export',
                            'parameters' => [
                                'format' => 'csv',
                            ],
                        ]
                    ])
            )
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/AdminProductGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\Product;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AdminProductGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_admin_product';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addActionGroup(
                BulkActionGroup::create(
                    Action::create('export', 'export')
                        ->setLabel('Export Data')
                        ->setOptions([
                            'link' => [
                                'route' => 'app_admin_product_export',
                                'parameters' => [
                                    'format' => 'csv',
                                ],
                            ]
                        ])
                )
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return Product::class;
    }
}
```
{% endcode %}
{% endtab %}
{% endtabs %}
