# Using autocompletes

[SyliusBootstrapAdminUi package](../../bootstrap-admin-ui/getting-started.md) uses [Symfony UX](https://ux.symfony.com/) under the hood.
Thus, UX autocomplete is already setup and configured in your admin panel.

## Configure entity autocomplete route for admin section

{% tabs %}
{% tab title="YAML" %}
{% code lineNumbers="true" %}
```yaml
# config/routes/ux_autocomplete.yaml
# ...
ux_entity_autocomplete_admin:
    path: '/admin/autocomplete/{alias}'
    controller: 'ux.autocomplete.entity_autocomplete_controller'
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code lineNumbers="true" %}
```php
<?php
// config/routes/ux_autocomplete.php
declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    // ...
    $routingConfigurator
        ->add('ux_entity_autocomplete_admin', '/admin/autocomplete/{alias}')
            ->controller('ux.autocomplete.entity_autocomplete_controller')
    ;
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

Now you have a new ajax route `ux_entity_autocomplete_admin` for your autocompletes.

## Add a grid filter with entity autocomplete

First, you need to create the [entity autocomplete field](https://symfony.com/bundles/ux-autocomplete/current/index.html#usage-in-a-form-with-ajax).

```php
<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Speaker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(
    alias: 'app_admin_speaker',
    route: 'ux_entity_autocomplete_admin', // We use the route we just configured before.
)]
final class SpeakerAutocompleteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Speaker::class,
            'choice_label' => 'fullName',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
```

Then you need to create the grid filter.

```php
<?php

declare(strict_types=1);

namespace App\Grid\Filter;

use App\Form\SpeakerAutocompleteType;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Filtering\ConfigurableFilterInterface;

final class SpeakerFilter implements ConfigurableFilterInterface
{
    public function __construct(
        private readonly EntityFilter $entityFilter,
    ) {
    }

    public function apply(DataSourceInterface $dataSource, string $name, mixed $data, array $options): void
    {
        $this->entityFilter->apply($dataSource, $name, $data, $options);
    }

    public static function getFormType(): string
    {
        return SpeakerAutocompleteType::class;
    }

    public static function getType(): string
    {
        return self::class; // it will allow to use FQCN instead of a string key.
    }
}
```

We also need to configure the Twig template for our new grid filter.

```yaml
# config/packages/sylius_grid.yaml
sylius_grid:
    # ...
    templates:
        filter:
            'App\Grid\Filter\SpeakerFilter': '@SyliusBootstrapAdminUi/shared/grid/filter/entity.html.twig'

```

Now our new grid filtered is configured, we can use it in a grid.

```php
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Grid\Filter\SpeakerFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class TalkGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    // ...

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            // ...
            ->addFilter(
                Filter::create(name: 'speaker', type: SpeakerFilter::class) // We use the new Speaker filter we just created.
                    ->setLabel('app.ui.speaker')
                    ->setOptions(['fields' => ['speakers.id']]),
            )
            // ...
        ;
    }
    
    // ...
}
```
