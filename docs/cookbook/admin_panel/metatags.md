# Customizing the metatags

## Adding metatags

To add new `<head>` meta tags, you can use the `sylius_admin#metatags` hook. This is useful for adding a favicon or SEO meta tags, for example.
You can register your own Twig template for meta tags via YAML or PHP.

{% tabs %}
{% tab title="YAML" %}
{% code title="config/packages/sylius_bootstrap_admin_ui.yaml" lineNumbers="true" %}
```yaml
# ...
sylius_twig_hooks:
    hooks:
        # ...
        'sylius_admin.base#metatags':
            favicon:
                template: 'favicon.html.twig'
            seo_metatags:
                template: 'seo_metatags.html.twig'
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code title="config/packages/sylius_bootstrap_admin_ui.php" lineNumbers="true" %}
```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // ...

    // Define your own Twig template for the favicon.
    $containerConfigurator->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.base#metatags' => [
                'favicon' => [
                    'template' => 'favicon.html.twig',
                ],
                'seo_metatags' => [
                    'template' => 'seo_metatags.html.twig',
                ],
            ],
        ],

    ]);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}
