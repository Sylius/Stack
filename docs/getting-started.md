# Getting started

## Setup an admin panel

The Sylius Stack comes with a bunch of components that work great independently, but when they come together, that's when the stack's magic truly operates! 
Indeed, the highlight of this project is the ability to configure an admin panel UI within minutes.

### Create a new project

You can set up the Sylius Stack on existing Symfony projects, but in the case you are starting from scratch, here is what you need to do.

```bash
# With Composer:
composer create-project symfony/skeleton my_project

# Or with Symfony CLI:
symfony new --docker --php 8.4 my_project
````

### Install the package using Composer and Symfony Flex

Go to your project directory and run the following command:

```bash
composer require -W \
  doctrine/orm \
  doctrine/doctrine-bundle \
  pagerfanta/doctrine-orm-adapter \
  symfony/asset-mapper \
  sylius/bootstrap-admin-ui \
  sylius/ui-translations
```

<div data-full-width="false">

<figure><img src=".gitbook/assets/recipes.png" alt="Flex recipes"></figure>

</div>

Type "a" or "p" to configure the packages via Symfony Flex.

### Run your web server

```bash
docker compose up -d
symfony serve -d
```

The admin panel is ready to use.
Now, it's your turn!

<div data-full-width="false">

<figure><img src=".gitbook/assets/admin-dashboard.png" alt="Admin dashboard overview"></figure>

</div>

### Using AssetMapper

To prevent duplicate Ajax calls, disable the auto-initialized Stimulus app and Symfony UX stylesheets from the `sylius/bootstrap-admin-ui` package, so you can take control of Stimulus initialization in your own code.

#### Disabling Stimulus app & Symfony UX stylesheets from third party package

First, you need to disable the Stimulus App started by the `sylius/bootstrap-admin-ui` package.

{% tabs %}
{% tab title="YAML" %}
{% code lineNumbers="true" %}
```yaml
# config/packages/sylius_bootstrap_admin_ui.yaml
# ...
sylius_twig_hooks:
    hooks:
        # ...
        # Disabling Symfony UX stylesheets
        'sylius_admin.base#stylesheets':
            symfony_ux:
                enabled: false    
           
        # Disabling Stimulus App        
        'sylius_admin.base#javascripts':
            symfony_ux:
                enabled: false
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code title="config/packages/sylius_bootstrap_admin_ui.php" lineNumbers="true" %}
```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // ...

    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.base#stylesheets' => [
                // Disabling Symfony UX stylesheets
                'symfony_ux' => [
                    'enabled' => false,
                ],
            ],
            
            'sylius_admin.base#javascripts' => [
                // Disabling Stimulus App        
                'symfony_ux' => [
                    'enabled' => false,
                ],
            ],
        ],    
    ]);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

#### Starting Stimulus App

```js
// assets/bootstrap.js
import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
```

```js
// assets/app.js
import './bootstrap.js';
// ...
```
