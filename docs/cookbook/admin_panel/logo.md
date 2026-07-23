# Customizing the logo

## How to customize the sidebar logo

To customize the sidebar logo, you need to set new logo template at `sylius_admin.common.component.sidebar.logo` twig hook. Choose the YAML or the PHP version.

{% tabs %}
{% tab title="YAML" %}
{% code lineNumbers="true" %}
```yaml
# config/packages/sylius_bootstrap_admin_ui.yaml
# ...
sylius_twig_hooks:
    hooks:
        # ...
        'sylius_admin.common.component.sidebar.logo':
            image:
                # template: '@SyliusBootstrapAdminUi/shared/crud/common/sidebar/logo/image.html.twig'
                template: 'shared/crud/common/sidebar/logo/image.html.twig'

```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code lineNumbers="true" %}
```php
// config/packages/sylius_bootstrap_admin_ui.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // ...

    // Add these following lines to define your own Twig template for the logo.
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.common.component.sidebar.logo' => [
                'image' => [
                    // 'template' => '@SyliusBootstrapAdminUi/shared/crud/common/sidebar/logo/image.html.twig',
                    'template' => 'shared/crud/common/sidebar/logo/image.html.twig',
                ],
            ],
        ],
        
    ]);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

```twig
{# templates/shared/crud/common/sidebar/logo/image.html.twig #}

<img src="{{ asset('images/logo.png') }}" alt="Your Brand name" class="navbar-brand-image" />


  
```

## How to customize the login page logo

To customize the login page logo,you need to set new logo template at `sylius_admin.security.login.logo` twig hook. Choose the YAML or the PHP version.

{% tabs %}
{% tab title="YAML" %}
{% code lineNumbers="true" %}
```yaml
# config/packages/sylius_bootstrap_admin_ui.yaml
# ...
sylius_twig_hooks:
    hooks:
        # ...
        'sylius_admin.security.login.page.logo':
            image:
                # template: '@SyliusBootstrapAdminUi/security/common/logo/image.html.twig'
                template: 'security/common/logo/image.html.twig'
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code lineNumbers="true" %}
```php
// config/packages/sylius_bootstrap_admin_ui.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // ...

    // Add the following lines to define your own Twig template for the logo.
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.security.login.logo' => [
                'image' => [
                    // 'template' => '@SyliusBootstrapAdminUi/security/common/logo/image.html.twig'
                    'template' => 'security/common/logo/image.html.twig',
                ],
            ],
        ],
    ]);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

```twig
<img src="{{ asset('images/logo.png') }}" alt="Your Brand name" class="sylius navbar-brand-image">
```
