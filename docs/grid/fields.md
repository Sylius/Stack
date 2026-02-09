# Fields

## Configuring Fields

Each field can be configured with several configuration keys, to make it
more suitable to your grid requirements.

| Name     | Type   | Description                                                                                         |
  |----------|--------|-----------------------------------------------------------------------------------------------------|
| type     | string | Type of column. Default field types are described [here](field_types.md).                           |
| label    | string | Label displayed in the field header. By default, it is field name.                                  |
| path     | string | Path to property displayed in field (can be property of resource or one of its referenced objects). |
| position | int    | Position of field in the grid index view.                                                           |
| options  | array  | Array of field options (see below).                                                                 |

The `options` field can itself contain the following fields:

| Name     | Type   | Description                                                                                                 | Default     |
  |----------|--------|-------------------------------------------------------------------------------------------------------------|-------------|
| template | string | Available (and required) only for *twig* column type. Path to template that is used to render column value. |             |
| format   | string | Available only for *datetime* field type.                                                                   | `Y:m:d` `H:i:s` |

## Field types

This is the list of built-in field types.

### String

The simplest column type, which displays the value at the specified path as plain text.

By default, it uses the name of the field, but you can specify a different path if needed. For example:

{% tabs %}
{% tab title="PHP (recommended)" %}
{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    name: 'app_user',
    resourceClass: User::class
)]
final class UserGrid extends AbstractGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFields(
                StringField::create('email')
                    ->setLabel('app.ui.email') // # each field type can have a label, we suggest using translation keys instead of messages
                    ->setPath('contactDetails.email')
            )
        ;
    }
}
```
{% endcode %}
{% endtab %}

{% tab title="YAML" %}
{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_user:
            fields:
                email:
                    type: string
                    label: app.ui.email # each field type can have a label, we suggest using translation keys instead of messages
                    path: contactDetails.email
```
{% endcode %}
{% endtab %}

{% tab title="PHP config file" %}
{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFields(
            StringField::create('email')
                ->setLabel('app.ui.email') // # each field type can have a label, we suggest using translation keys instead of messages
                ->setPath('contactDetails.email')
        )
    )
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

This configuration will display the value of `$user->getContactDetails()->getEmail()`.

### DateTime

This column type works exactly the same way as _StringField_, but expects a _DateTime_ instance and outputs a formatted date and time string.

Available options:

* `format` - defaults to `Y:m:d H:i:s`, you can set it to any supported format (see [https://www.php.net/manual/en/datetime.format.php](https://www.php.net/manual/en/datetime.format.php))
* `timezone` - defaults to `%sylius_grid.timezone%` parameter, null if such a parameter does not exist, you can set it to any supported timezone (see [https://www.php.net/manual/en/timezones.php](https://www.php.net/manual/en/timezones.php))

{% tabs %}
{% tab title="PHP (recommended)" %}
{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    name: 'app_user',
    resourceClass: User::class
)]
final class UserGrid extends AbstractGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFields(
                DateTimeField::create('birthday', 'Y:m:d H:i:s', null) // this format and timezone are the default value, but you can modify them
                    ->setLabel('app.ui.birthday')
            )
        ;
    }
}
```
{% endcode %}
{% endtab %}

{% tab title="YAML" %}
{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_user:
            fields:
                birthday:
                    type: datetime
                    label: app.ui.birthday
                    options:
                        format: 'Y:m:d H:i:s'
                        timezone: null
```
{% endcode %}
{% endtab %}

{% tab title="PHP config file" %}
{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFields(
            DateTimeField::create('birthday', 'Y:m:d H:i:s', null) // this format and timezone are the default value, but you can modify them
                ->setLabel('app.ui.birthday')
        )
    )
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

{% hint style="warning" %}
If you want to call the `setOptions` function, you must pass both `'format'` and `'timezone'` as arguments again. Otherwise, they will be unset.

```php
$field->setOptions([
    'format' => 'Y-m-d H:i:s',
    'timezone' => 'null'

    // Your options here
]);
```
{% endhint %}

### Twig

The Twig column type is the most flexible one, because it delegates the logic of rendering the value to the Twig templating engine. First, you must specify the template you want to render.

{% tabs %}
{% tab title="PHP (recommended)" %}
{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    name: 'app_user',
    resourceClass: User::class
)]
final class UserGrid extends AbstractGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFields(
                TwigField::create('name', ':Grid/Column:_prettyName.html.twig')
                    ->setLabel('app.ui.name')
            )
        ;
    }
}
```
{% endcode %}
{% endtab %}

{% tab title="YAML" %}
{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_user:
            fields:
                name:
                    type: twig
                    label: app.ui.name
                    options:
                        template: "@Grid/Column/_prettyName.html.twig"
```
{% endcode %}
{% endtab %}

{% tab title="PHP config file" %}
{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFields(
            TwigField::create('name', '@Grid/Column/_prettyName.html.twig')
                ->setLabel('app.ui.name')
        )
    )
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

Then, within the template, you can render the field's value via the `data` variable.

{% code title="@Grid/Column/_prettyName.html.twig" %}
```twig
<strong>{{ data }}</strong>
```
{% endcode %}

#### Binding a Field to the Full Object Instance

To render more complex data in a grid field, you can bind the field to the root object by redefining the field path. This gives you access to all attributes of the underlying object when rendering the field.

{% tabs %}
{% tab title="PHP (recommended)" %}
{% code title="src/Grid/UserGrid.php" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    name: 'app_user',
    resourceClass: User::class
)]
final class UserGrid extends AbstractGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFields(
                TwigField::create('name', ':Grid/Column:_prettyName.html.twig')
                    ->setLabel('app.ui.name')
                    ->setPath('.') // sets the field path to the root object
            )
        ;
    }
}
```
{% endcode %}
{% endtab %}

{% tab title="YAML" %}
{% code title="config/packages/sylius_grid.yaml" %}
```yaml
sylius_grid:
    grids:
        app_user:
            fields:
                name:
                    type: twig
                    label: app.ui.name
                    path: .    # sets the field path to the root object
```
{% endcode %}
{% endtab %}

{% tab title="PHP config file" %}
{% code title="config/packages/sylius_grid.php" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFields(
            TwigField::create('name', '@Grid/Column/_prettyName.html.twig')
                ->setLabel('app.ui.name')
                ->setPath('.') // sets the field path to the root object
        )
    )
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

This allows you to render multiple properties inside the same field Twig template.

```twig
<strong>{{ data.name }}</strong>
<p>{{ data.description|markdown }}</p>
```

{% hint style="warning" %}
If you want to call the `setOptions` function, you must pass `'template'` as an argument again. Otherwise, it will be unset.

```php
$field->setOptions([
    'template' => ':Grid/Column:_prettyName.html.twig',

    // Your options here
]);
```
{% endhint %}

## Creating a custom Field Type

There are certain cases when built-in field types are not enough. Sylius
Grids make it easy to define new types.

All you need to do is create your own class implementing
**FieldTypeInterface** and register it as a service.

{% code title="src/Grid/FieldType.php" lineNumbers="true" %}

```php
<?php

namespace App\Grid\FieldType;

use Sylius\Component\Grid\Attribute\AsField;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsField(
    type: 'custom', // optional - FQCN by default
)]
final class CustomType implements FieldTypeInterface
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
}
```
{% endcode %}

That is all.

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
