# Filters

*Filters* on grids act as predefined search options for each grid. Having
a grid of objects you can filter out only those with a specified name,
or value etc. Here you can find the supported filters. Keep in mind you
can very easily define your own ones!

## String

Simplest filter type. It can filter by one or multiple fields.

**Filter by one field**

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_user:
            filters:
                username:
                    type: string
                email:
                    type: string
                firstName:
                    type: string
                lastName:
                    type: string
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('username', 'string'),
            Filter::create('email', 'string'),
            Filter::create('firstName', 'string'),
            Filter::create('lastName', 'string'),
        )
    
        // can be simplified using StringFilter
        ->withFilters(
            StringFilter::create('username'),
            StringFilter::create('email'),
            StringFilter::create('firstName'),
            StringFilter::create('lastName'),
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('username', 'string'),
                Filter::create('email', 'string'),
                Filter::create('firstName', 'string'),
                Filter::create('lastName', 'string'),
            )
            
            // can be simplified using StringFilter
            ->withFilters(
                StringFilter::create('username'),
                StringFilter::create('email'),
                StringFilter::create('firstName'),
                StringFilter::create('lastName'),
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

**Filter by multiple fields**

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_user:
            filters:
                search:
                    type: string
                    options:
                        fields: [username, email, firstName, lastName]
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('username', 'string')
                ->setOptions(['fields' => ['username', 'email', 'firstName', 'lastName']])
        )
    
        // can be simplified using StringFilter
        ->withFilters(
            StringFilter::create('username', ['username', 'email', 'firstName', 'lastName'])
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('username', 'string')
                    ->setOptions(['fields' => ['username', 'email', 'firstName', 'lastName']])
            )
            
            // can be simplified using StringFilter
            ->withFilters(
                StringFilter::create('username', ['username', 'email', 'firstName', 'lastName'])
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

**Search options**

This filter allows the user to select the following search options:

-   contains
-   not contains
-   equal
-   not equal
-   starts with
-   ends with
-   empty
-   not empty
-   in
-   not in
-   member of

If you don't want to display all these matching possibilities, you can
choose just one of them. Then only the input field will be displayed.
You can achieve it like this:

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_user:
            filters:
                username:
                    type: string
                    form_options:
                        type: contains
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('username', 'string')
                ->setFormOptions([
                    'type' => 'contains',
                ])
        )
        
        // can be simplified using StringFilter
        ->withFilters(
            StringFilter::create('username', null, 'contains')
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('username', 'string')
                    ->setFormOptions([
                        'type' => 'contains',
                    ])
            )
            
            // can be simplified using StringFilter
            ->withFilters(
                StringFilter::create('username', null, 'contains')
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

By configuring the filter as shown above, 
you will create an input field that filters user objects based on whether their username `contains` a given string.

## Boolean

<details open><summary>Yaml</summary>

This filter checks if a value is true or false.

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_channel:
            filters:
                enabled:
                    type: boolean
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('enabled', 'boolean')
        )
        
        // can be simplified using BooleanFilter
        ->withFilters(
            BooleanFilter::create('enabled')
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('enabled', 'boolean')
            )
            
            // can be simplified using BooleanFilter
            ->withFilters(
                BooleanFilter::create('enabled')
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

## Date

This filter checks if a chosen datetime field is between given dates.

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_order:
            filters:
                createdAt:
                    type: date
                completedAt:
                    type: date
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('createdAt', 'date'),
            Filter::create('completedAt', 'date'),
        )
        
        // can be simplified using DateFilter
        ->withFilters(
            DateFilter::create('createdAt'),
            DateFilter::create('completedAt'),
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('createdAt', 'date'),
                Filter::create('completedAt', 'date'),
            )
            
            // can be simplified using DateFilter
            ->withFilters(
                DateFilter::create('createdAt'),
                DateFilter::create('completedAt'),
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

## Entity

This type filters by a chosen entity.

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_order:
            filters:
                channel:
                    type: entity
                    form_options:
                        class: "%app.model.channel.class%"
                        # You can pass any form options available in Entity Type
                        # See https://symfony.com/doc/current/reference/forms/types/entity.html
                        multiple: true 
                customer:
                    type: entity
                    form_options:
                        class: "%app.model.customer.class%"
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('channel', 'entity')
                ->setFormOptions([
                    'class' => '%app.model.channel.class%'
                    // You can pass any form options available in Entity Type
                    // See https://symfony.com/doc/current/reference/forms/types/entity.html
                    'multiple' => true,
                ]),
            Filter::create('customer', 'entity')
                ->setFormOptions(['class' => '%app.model.customer.class%']),    
        )
        
        // can be simplified using EntityFilter
        ->withFilters(
            EntityFilter::create('channel', '%app.model.channel.class%')
                ->addFormOption('multiple', true),
            EntityFilter::create('customer', '%app.model.customer.class%'),
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('channel', 'entity')
                    ->setFormOptions(['class' => '%app.model.channel.class%']),
                Filter::create('customer', 'entity')
                    ->setFormOptions(['class' => '%app.model.customer.class%']),
            )
            
            // can be simplified using EntityFilter
            ->withFilters(
                EntityFilter::create('channel', '%app.model.channel.class%'),
                EntityFilter::create('customer', '%app.model.customer.class%'),
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

## Money

This filter checks if an amount is within the specified range and is in the selected currency

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_order:
            filters:
                total:
                    type: money
                    form_options:
                        scale: 3
                    options:
                        currency_field: currencyCode
                        scale: 3
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\MoneyFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('total', 'money')
                ->setFormOptions(['scale' => 3])
                ->setOptions([
                    'currency_field' => 'currencyCode',
                    'scale' => 3,
                ])
        )
        
        // can be simplified using MoneyFilter
        ->withFilters(
            MoneyFilter::create('total', 'currencyCode', 3)
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\MoneyFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('total', 'money')
                    ->setFormOptions(['scale' => 3])
                    ->setOptions([
                        'currency_field' => 'currencyCode',
                        'scale' => 3,
                    ])
            )
            
            // can be simplified using MoneyFilter
            ->withFilters(
                MoneyFilter::create('total', 'currencyCode', 3)
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

### *Warning*

Providing different `scale` values between *form_options* and *options*
may cause unwanted, and plausibly volatile results.

## Exists

This filter checks if the specified field contains any value

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_order:
            filters:
                date:
                    type: exists
                    options:
                        field: completedAt
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\ExistsFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('date', 'exists')
                ->setOptions(['field' => 'completedAt'])
        )
        
        // can be simplified using ExistsFilter
        ->withFilters(
            ExistsFilter::create('date', 'completedAt')
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\ExistsFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('date', 'exists')
                    ->setOptions(['field' => 'completedAt'])
            )
            
            // can be simplified using ExistsFilter
            ->withFilters(
                ExistsFilter::create('date', 'completedAt')
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

## Select

This type filters by a value chosen from the defined list

<details open><summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        app_order:
            filters:
                state:
                    type: select
                    form_options:
                        choices:
                            sylius.ui.ready: Ready
                            sylius.ui.shipped: Shipped
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->withFilters(
            Filter::create('state', 'select')
                ->setFormOptions([
                    'choices' => [
                        'sylius.ui.ready' => 'Ready',
                        'sylius.ui.shipped' => 'Shipped',
                    ],
                ])
        )
        
        // can be simplified using SelectFilter
        ->withFilters(
            SelectFilter::create('state', [
                'sylius.ui.ready' => 'Ready',
                'sylius.ui.shipped' => 'Shipped',
            ])
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/UserGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('state', 'select')
                    ->setFormOptions([
                        'choices' => [
                            'sylius.ui.ready' => 'Ready',
                            'sylius.ui.shipped' => 'Shipped',
                        ],
                    ])
            )
            
            // can be simplified using SelectFilter
            ->withFilters(
                SelectFilter::create('state', [
                    'sylius.ui.ready' => 'Ready',
                    'sylius.ui.shipped' => 'Shipped',
                ])
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```
{% endcode %}

</details>

## Creating a custom Filter

Sylius Grids come with built-in filters, but there are use-cases where you need something more than a basic filter. Grids let you define your own filter types!

To add a new filter, we need to create an appropriate class and form type.

{% tabs %}
{% tab title="SyliusGridBundle v1.14 - using attributes" %}
{% code title="src/Grid/Filter/SuppliersStatisticsFilter.php" lineNumbers="true" %}
```php
<?php
 
declare(strict_types=1);
 
namespace App\Grid\Filter;

use App\Form\Type\Filter\SuppliersStatisticsFilterType;
use Sylius\Bundle\GridBundle\Doctrine\DataSourceInterface;
use Sylius\Component\Grid\Attribute\AsFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;
  
#[AsFilter(
    formType: SuppliersStatisticsFilterType::class,  // (custom) Symfony FormType
    template: '@SyliusBootstrapAdminUi/shared/grid/filter/select.html.twig',  // or you can use your own Twig template
    type: 'suppliers_statistics',  // optional - FQCN by default
)]
class SuppliersStatisticsFilter implements FilterInterface
{
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options = []): void
    {
        // Your filtering logic.
        // $data['stats'] contains the submitted value!
        $queryBuilder = $dataSource->getQueryBuilder();
        $queryBuilder
            ->andWhere('stats = :stats')
            ->setParameter(':stats', $data['stats'])
        ;
    
        // You can leverage the ExpressionBuilder to apply driver-agnostic filters to the data source.
        // Combined with restrict(), it provides query builder–style functionalities for grid filters.
        $dataSource->restrict($dataSource->getExpressionBuilder()->equals('stats', $data['stats']));
    }
}
```
{% endcode %}

And the form type:

{% code title="src/Form/Type/Filter/SuppliersStatisticsFilterType.php" lineNumbers="true" %}
```php
<?php

namespace App\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuppliersStatisticsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'stats',
            ChoiceType::class,
            ['choices' => range($options['range'][0], $options['range'][1])]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'range' => [0, 10],
            ])
            ->setAllowedTypes('range', ['array'])
        ;
    }
}
```
{% endcode %}
{% endtab %}

{% tab title="SyliusGridBundle v1.13" %}
{% code title="src/Grid/Filter/SuppliersStatisticsFilter.php" lineNumbers="true" %}
```php
<?php

namespace App\Grid\Filter;

use App\Form\Type\Filter\SuppliersStatisticsFilterType;
use Sylius\Bundle\GridBundle\Doctrine\DataSourceInterface;
use Sylius\Component\Grid\Filtering\ConfigurableFilterInterface;

class SuppliersStatisticsFilter implements ConfigurableFilterInterface
{
    public function apply(DataSourceInterface $dataSource, $name, $data, array $options = []): void
    {
        // Your filtering logic.
        // $data['stats'] contains the submitted value!
        $queryBuilder = $dataSource->getQueryBuilder();
        $queryBuilder
            ->andWhere('stats = :stats')
            ->setParameter(':stats', $data['stats'])
        ;
    
        // For driver abstraction you can use the expression builder. ExpressionBuilder is a kind of query builder.
        $dataSource->restrict($dataSource->getExpressionBuilder()->equals('stats', $data['stats']));
    }
    
    public static function getType() : string
    {
        return 'suppliers_statistics';
    }
    
    public static function getFormType() : string
    {
        return SuppliersStatisticsFilterType::class;
    }
}
```
{% endcode %}

And the form type:

{% code title="src/Grid/Filter/SuppliersStatisticsFilterType.php" lineNumbers="true" %}
```php
<?php

namespace App\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuppliersStatisticsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'stats',
            ChoiceType::class,
            ['choices' => range($options['range'][0], $options['range'][1])]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'range' => [0, 10],
            ])
            ->setAllowedTypes('range', ['array'])
        ;
    }
}
```
{% endcode %}

Create a template for the filter, similar to the existing ones:

{% code title="templates/grid/filter/suppliers_statistics.html.twig" lineNumbers="true" %}
```twig

<div data-gb-custom-block data-tag="form_theme" data-0='@SyliusUi/Form/theme.html.twig'></div>

{{ form_row(form) }}
```
{% endcode %}

If you use autoconfiguration, the filter is automatically registered as a grid filter.

But if you don't use autoconfiguration, let's register your new filter type as a service.

{% code title="config/services.yaml" lineNumbers="true" %}
```yaml
services:
    App\Grid\Filter\SuppliersStatisticsFilter:
        tags: ['sylius.grid_filter']
```
{% endcode %}
{% endtab %}
{% endtabs %}

Now you can use your new filter type in any grid configuration!&#x20;

{% tabs %}
{% tab title="PHP" %}
{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use App\Entity\Tournament;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_tournament', Tournament::class)
        ->withFilters(
            Filter::create('stats', 'suppliers_statistics')
                ->setFormOptions(['range' => [0, 100]])
        )
    )
};
```
{% endcode %}

OR

{% code title="src/Grid/TournamentGrid.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\Tournament;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

#[AsGrid(
     name: 'app_tournament',
     resourceClass: Tournament::class,
)]
final class TournamentGrid extends AbstractGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                Filter::create('stats', 'suppliers_statistics')
                    ->setFormOptions(['range' => [0, 100]])
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
        app_tournament:
            driver: doctrine/orm
            resource: app.tournament
            filters:
                stats:
                    type: suppliers_statistics
                    form_options:
                        range: [0, 100]
    
    templates:  # only needed if you didn't use AsFilter attribute
        filter:
            suppliers_statistics: '@App/Grid/Filter/suppliers_statistics.html.twig'
```
{% endcode %}
{% endtab %}
{% endtabs %}

