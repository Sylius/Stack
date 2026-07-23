# Advanced configuration

By default, Doctrine options `fetchJoinCollection` and `useOutputWalkers` are enabled in all grids, but you can simply disable them with this config:

<details>

<summary>Yaml</summary>

{% code title="config/packages/sylius_grid.yaml" lineNumbers="true" %}
```yaml
sylius_grid:
    grids:
        foo:
            driver:
                options:
                    pagination:                
                        fetch_join_collection: false
                        use_output_walkers: false
```
{% endcode %}

</details>

<details>

<summary>PHP</summary>

{% code title="config/packages/sylius_grid.php" lineNumbers="true" %}
```php
<?php

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->setDriverOption('pagination', [
            'fetch_join_collection' => false,
            'use_output_walkers' => false,
        ])
    )
};
```
{% endcode %}

</details>

These changes may be necessary when you work with huge databases.
