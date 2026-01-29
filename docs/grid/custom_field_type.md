# Creating a custom Field Type

There are certain cases when built-in field types are not enough. Sylius
Grids make it easy to define new types.

All you need to do is create your own class implementing
**FieldTypeInterface** and register it as a service.

{% code title="src/Grid/FieldType.php" lineNumbers="true" %}
```php
<?php

namespace App\Grid\FieldType;

use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomType implements FieldTypeInterface
{
    public function render(Field $field, $data, array $options = [])
    {
        // Your rendering logic... Use Twig, PHP or even external api...
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'dynamic' => false
            ])
            ->setAllowedTypes([
                'dynamic' => ['boolean']
            ])
        ;
    }

    public function getName(): string
    {
        return 'custom';
    }
}
```
{% endcode %}

That is all. Now register your new field type as a service.

{% code title="config/services.yaml" lineNumbers="true" %}
```yaml
app.grid_field.custom:
    class: App\Grid\FieldType\CustomType
    tags:
        - { name: sylius.grid_field, type: custom }
```
{% endcode %}

Now you can use your new column type in the grid configuration!

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
            fields:
                name:
                    type: custom
                    label: sylius.ui.name
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
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_admin_supplier', Supplier::class)
        ->withFields(
            Field::create('name', 'custom')
                ->setLabel('sylius.ui.name')
        )
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
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
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
            ->withFields(
                Field::create('name', 'custom')
                    ->setLabel('sylius.ui.name')
            )
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
