# Autoprefixing feature

{% hint style="warning" %}
`Autoprefixing` is turned off by default. If you want to use this feature you need to set the `enable_autoprefixing` setting to `true` in your `config/packages/twig_hooks.yaml` file:

{% tabs %}
{% tab title="YAML" %}
{% code lineNumbers="true" %}
```yaml
# config/packages/sylius_twig_hooks.yaml
sylius_twig_hooks:
    # ...
    enable_autoprefixing: true
    # ...
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code lineNumbers="true" %}
```php
// config/packages/sylius_twig_hooks.php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'enable_autoprefixing' => true,
    ]);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

{% endhint %}

When you are creating a bundle, or a bigger project like [Sylius](https://sylius.com), you might want to rely fully on Twig Hooks to provide easy and flexible way of modifying and extending your views.

Enabling the autoprefixing feature might improve your developer experience. This feature is crucial for creating [composable-layouts-with-a-predictable-structure.md](composable-layouts-with-a-predictable-structure.md "mention").

{% hint style="info" %}
If you did not read the [composable-layouts-with-a-predictable-structure.md](composable-layouts-with-a-predictable-structure.md "mention")section we encourage you to do it before you read more about the autoprefixing feature.&#x20;
{% endhint %}

The mechanism of autoprefixing is pretty simple. We check if there are any prefixes, then we iterate over them and prepend the hook name with a given prefix.

### Defining prefixes

Prefixes by default are injected automatically, and they are the name of the hook where the hookable is rendered.

```gherkin
Given As a developer I define the "index.form" hook in my template
And I define the "some_field" hookable in it
When I check prefixes inside the "some_field" hookable 
Then I should get "index.form"
```

In case we deal with a complex hook:

```gherkin
Given As a developer I define the "index.form" and common.form" hooks in my template
And I define the "some_field" hookable in "index.form"
When I check prefixes inside the "some_field" hookable 
Then I should get "index.form" and "common.form"
```

If for some reason you want to take the control over the passed prefixes, you can override existing prefixes using the `_prefixes` magic variable when you are creating a hook inside a Twig template:

```twig
{% raw %}
<!-- templates/index.html.twig -->
{% hook 'index.form' with {
    _prefixes: ['my_custom_prefix']
} %}
{% endraw %}
```

From now, only the value of `_prefixes` will be taken into account.

### Example

```twig
{% raw %}
<!-- templates/index.html.twig -->
{% hook 'app.index' %}
{% endraw %}
```

`index.html.twig` is an entry template, so it is not an hookable.

```twig
{% raw %}
<!-- templates/index/content.html.twig -->
{% hook 'content' %}
{% endraw %}
```

This template is an hookable, and is hooked into `app.index`
So `hook 'content'` this is a shorter form of `hook 'app.index.content'` when autoprefixing is turned on.

```twig
{% raw %}
<!-- templates/index/content/button.html.twig -->
<button>Click me!</button>
{% endraw %}
```

The configuration for the hooks and hookables above is:

{% tabs %}
{% tab title="YAML" %}
{% code lineNumbers="true" %}
```yaml
# config/packages/sylius_twig_hooks.yaml
sylius_twig_hooks:
    hooks:
        'app.index':
            content:
                template: 'index/content.html.twig'

        'app.index.content':
            button:
                template: 'index/content/button.html.twig
```
{% endcode %}
{% endtab %}

{% tab title="PHP" %}
{% code lineNumbers="true" %}
```php
// config/packages/sylius_twig_hooks.php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'hooks' => [
            'app.index' => [
                'content' => [
                    'template' => 'index/content.html.twig',
                ],
            ],
            
            'app.index.content' => [
                'button' => [
                    'template' => 'index/content/button.html.twig',
                ],
            ],
        ],
    ]);
};
```
{% endcode %}
{% endtab %}
{% endtabs %}

{% hint style="info" %}
The structure of directories above does not matter, all templates can be on the same level of nesting. However, in this example we are following creating [composable-layouts-with-a-predictable-structure.md](composable-layouts-with-a-predictable-structure.md "mention") guide.
{% endhint %}
