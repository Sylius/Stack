# Using autocompletes

The [SyliusBootstrapAdminUi](../../bootstrap-admin-ui/getting-started.md) package uses[ Symfony UX ](https://ux.symfony.com/)under the hood. Thus, [UX autocomplete](https://symfony.com/bundles/ux-autocomplete/current/index.html) is already setup and configured in your admin panel.  This means that any simple `ChoiceType` filter form can be turned into an autocomplete simply by setting `autocomplete` to `true` in form options.

```php
public function configureOptions(OptionsResolver $resolver): void
{
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'placeholder' => 'sylius.ui.all',
            'autocomplete' => true,
        ]);
}

public function getParent(): string
{
    return ChoiceType::class;
}

```

&#x20;However, if your autocomplete filter requires fetching data from another entity, you will need to use a `BaseEntityAutocompleteType` in order to fetch your options via AJAX.

All you need to start leveraging this functionality is a bit of routing config.

### Configure the entity autocomplete route

{% tabs %}
{% tab title="PHP" %}
{% code title="config/routes/ux_autocomplete.php" %}
```php
<?php

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

{% tab title="YAML" %}
{% code title="config/routes/ux_autocomplete.yaml" %}
```yaml
# ...
ux_entity_autocomplete_admin:
    path: '/admin/autocomplete/{alias}'
    controller: 'ux.autocomplete.entity_autocomplete_controller'
```
{% endcode %}
{% endtab %}
{% endtabs %}

This adds a new  `ux_entity_autocomplete_admin` AJAX route dedicated to your autocompletes.

### Add a grid filter with entity autocomplete

First, you need to create an [entity autocomplete field ](https://symfony.com/bundles/ux-autocomplete/current/index.html#usage-in-a-form-with-ajax).

{% code title="src/Form/SpeakerAutocompleteType.php" lineNumbers="true" %}
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
    route: 'ux_entity_autocomplete_admin', // Use the route you just configured.
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
{% endcode %}

Then, you need to create your grid filter.

{% tabs %}
{% tab title="SyliusGridBundle v1.13 (latest)" %}
1\) Create your filter class.

{% code title="src/Grid/Filter/SpeakerFilter.php" lineNumbers="true" %}
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
        return self::class; // this will allow us to use FQCN instead of a string key.
    }
  
  }
```
{% endcode %}

2\) then configure the Twig template this filter will use.

PHP config :&#x20;

{% code title="config/packages/sylius_grid.php
" %}
```php
<?php

declare(strict_types=1);

use App\Grid\Filter\SpeakerFilter;
use App\Grid\Template\FilterTemplate;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('sylius_grid', [
        'templates' => [
            'filter' => [
               SpeakerFilter::class => '@SyliusBootstrapAdminUi/shared/grid/filter/select.html.twig',
            ],
        ],
    ]);
};
```
{% endcode %}

YAML config:

{% code title="config/packages/sylius_grid.yaml" %}
```yaml
sylius_grid:
    # ...
    templates:
        filter:
            'App\Grid\Filter\SpeakerFilter': '@SyliusBootstrapAdminUi/shared/grid/filter/entity.html.twig'
```
{% endcode %}
{% endtab %}

{% tab title="SyliusGridBundle v1.14@alpha" %}
{% code title="src/Grid/Filter/SpeakerFilter.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

namespace App\Grid\Filter;

use App\Form\SpeakerAutocompleteType;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Filtering\FilterInterface;

#[AsFilter(
    formType: SpeakerAutocompleteType::class,
    template: '@SyliusBootstrapAdminUi/shared/grid/filter/select.html.twig', // optional
)]
final class SpeakerFilter implements FilterInterface
{
    public function __construct(
        private readonly EntityFilter $entityFilter,
    ) {
    }

    public function apply(DataSourceInterface $dataSource, string $name, mixed $data, array $options): void
    {
        $this->entityFilter->apply($dataSource, $name, $data, $options);
    }
  }
```
{% endcode %}
{% endtab %}
{% endtabs %}

Now that the filter is configured, you can use it inside any grid.

{% code title="src/Grid/TalkGrid.php" lineNumbers="true" %}
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
                Filter::create(name: 'speaker', type: SpeakerFilter::class)
                    ->setLabel('app.ui.speaker')
                    ->setOptions(['fields' => ['speakers.id']]),
            )
            // ...
        ;
    }
    
    // ...
}
```
{% endcode %}
