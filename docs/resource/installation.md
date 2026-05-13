# Installation

We assume you're familiar with [Composer](http://packagist.org), a dependency manager for PHP.
Use the following command to add the bundle to your ``composer.json`` and download the package.

If you have [Composer installed globally](http://getcomposer.org/doc/00-intro.md#globally).

```bash
composer require sylius/resource-bundle
```
Otherwise, you have to download .phar file.

```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar require sylius/resource-bundle
```
## Adding Required Bundles to The Kernel

You need to enable the bundle and its dependencies in the kernel:

{% code title="config/bundles.php" %}
```php
return [
    new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
    new BabDev\PagerfantaBundle\BabDevPagerfantaBundle(),
];
```
{% endcode %}

Configure your mapping paths for your resources :

{% tabs %}
{% tab title="PHP" %}
{% code title="config/packages/sylius_resource.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
    namespace: 'sylius_resource',
     config: [
        'mapping' => [
            'paths' => [
                '%kernel.project_dir%/src/Entity',
            ],
        ],
        'resources' => null,
    ]);
};
```
{% endcode %}
{% endtab %}
{% tab title="YAML" %}
{% code title="config/packages/sylius_resource.yaml" lineNumbers="true" %}
```yaml
sylius_resource:
    mapping:
        paths:
            - '%kernel.project_dir%/src/Entity'
```
{% endcode %}
{% endtab %}
{% endtabs %}

Configure the routing

{% tabs %}
{% tab title="PHP" %}
{% code title="config/packages/sylius_resource.php" lineNumbers="true" %}
```php
<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(resource: 'sylius.symfony.routing.loader.resource', type: 'service');
};
```
{% endcode %}
{% endtab %}
{% tab title="YAML" %}
{% code title="config/routes/sylius_resource.yaml" %}
```yaml
sylius_resource_routes:
    resource: 'sylius.symfony.routing.loader.resource'
    type: service
```
{% endcode %}
{% endtab %}
{% endtabs %}


That's it! Now you can configure your first resource.
