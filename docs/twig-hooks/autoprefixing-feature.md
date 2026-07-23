# Autoprefixing feature

## Autoprefixing feature

{% hint style="warning" %}
`Autoprefixing` is turned off by default. If you want to use this feature you need to set the `enable_autoprefixing` setting to `true` in your `config/packages/twig_hooks.yaml` file:

```yaml
sylius_twig_hooks:
    # ...
    enable_autoprefixing: true
    # ...
```
{% endhint %}

When you are creating a bundle, or a bigger project like [Sylius](https://sylius.com), you might want to rely fully on Twig Hooks to provide easy and flexible way of modifying and extending your views.

Enabling the autoprefixing feature might improve your developer experience. This feature is crucial for creating [composable-layouts-with-a-predictable-structure.md](composable-layouts-with-a-predictable-structure.md "mention").

{% hint style="info" %}
If you did not read the [composable-layouts-with-a-predictable-structure.md](composable-layouts-with-a-predictable-structure.md "mention") section we encourage you to do it before you read more about the autoprefixing feature.
{% endhint %}

The mechanism of autoprefixing is pretty simple. We check if there are any prefixes, then we iterate over them and prepend the hook name with a given prefix.

#### Defining prefixes

Prefixes by default are injected automatically, and they are the name of the hook where the hookable is rendered.

> As a developer I define the **index.form** hook in my template
>
> And I define the **some\_field** hookable in it
>
> So when I check prefixes **inside** the **some\_field** hookable I should get `index.form`

In case we deal with a complex hook:

> As a developer I define the **index.form, common.form** hook in my template
>
> And I define the **some\_field** hookable in **index.form**
>
> So when I check prefixes **inside** the **some\_field** hookable I should get `index.form` and `common.form`

If for some reason you want to take the control over the passed prefixes, you can override existing prefixes using the `_prefixes` magic variable when you are creating a hook inside a Twig template:

{% code title="index.html.twig" lineNumbers="true" %}
```twig
{% hook 'index.form' with {
    _prefixes: ['my_custom_prefix']
} %}
```
{% endcode %}

From now, only the value of `_prefixes` will be taken into account.

#### Example

{% code title="index.html.twig" lineNumbers="true" %}
```twig
{% hook 'app.index' %}

{# index.html.twig is an entry template, so it is not an hookable #}
```
{% endcode %}

<pre class="language-twig" data-title="index/content.html.twig" data-line-numbers><code class="lang-twig"><strong>{% hook 'content' %}
</strong>
{# this template is an hookable, and is hooked into app.index #}

{#
 # so {% hook 'content' %} this is a shorter form of {% hook 'app.index.content' %}
 # when autoprefixing is turned on
 #}
</code></pre>

{% code title="index/content/button.html.twig" lineNumbers="true" %}
```twig
<button>Click me!</button>
```
{% endcode %}

The configuration for the hooks and hookables above is:

{% code title="config/packages/twig_hooks.yaml" lineNumbers="true" %}
```yaml
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

{% hint style="info" %}
The structure of directories above does not matter, all templates can be on the same level of nesting. However, in this example we are following creating [composable-layouts-with-a-predictable-structure.md](composable-layouts-with-a-predictable-structure.md "mention") guide.
{% endhint %}
