# How to customize your admin panel

## How to customize the sidebar logo

```php
// config/packages/sylius_bootstrap_admin_ui.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@SyliusBootstrapAdminUiBundle/config/app.php');

    // Add these following lines to define your own Twig template for the logo.
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.common.component.sidebar' => [
                'logo' => [
                    'template' => 'shared/crud/common/sidebar/logo.html.twig',
                ],
            ],
        ],
    ]);
};

```

```twig
{% set dashboard_path = hookable_metadata.context.routing.dashboard_path|default('/admin') %}

<h1 class="navbar-brand">
    <a href="{{ dashboard_path }}">
        <img src="{{ asset('images/logo.png') }}" alt="CFC" class="navbar-brand-image" />
    </a>
</h1>

```

## How to customize the login page logo

```yaml
# config/packages/sylius_bootstrap_admin_ui.yaml
```

```php
// config/packages/sylius_bootstrap_admin_ui.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@SyliusBootstrapAdminUiBundle/config/app.php');

    // Add these following lines to define your own Twig template for the logo.
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.security.login' => [
                'logo' => [
                    // 'template' => '@SyliusBootstrapAdminUi/security/common/logo.html.twig'
                    'template' => 'security/common/logo.html.twig',
                ],
            ],
        ],
    ]);
};
```

```twig
<div class="text-center mb-4">
    <img src="{{ asset('images/logo.png') }}" alt="Your Brand name" class="sylius navbar-brand-image">
</div>

```
