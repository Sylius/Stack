# Creating a custom Filter

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
