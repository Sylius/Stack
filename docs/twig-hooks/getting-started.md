---
description: >-
  Twig Hooks are a robust and powerful alternative to the Sonata Block Events
  and the old Sylius Template Events systems.
---

# Getting started

### Main features

* built-in support for _Twig templates_, _Twig Components_ and _Symfony Live Components_
* adjustability
* autoprefixing hooks
* configurable hookables
* priority mechanism
* easy enable/disable mechanism for each hook

### Installation

Install the package using Composer and Symfony Flex:

```bash
composer require sylius/twig-hooks
```

### Your first hook & hookable

Once Twig Hooks is installed, you can open **any** Twig file and define your first hook.

{% code title="some.html.twig" %}
```twig
{% hook 'my_first_hook' %}
```
{% endcode %}

This way, `my_first_hook` becomes a unique name which we can use to hook into that specific spot.

{% hint style="success" %}
The ideal hook name:

* is lowercase
* has its logical parts separated with dots (`.`)
* when there is more than one word, they are separated by underscores (`_`)

<mark style="color:green;">Recommended:</mark>

* `index`
* `index.sidebar`
* `index.top_menu`

<mark style="color:red;">Not recommended:</mark>

* index
* indextopmenu
{% endhint %}

**Hooking into a hook**

For the purpose of this example, let's consider we want to render the `some_block.html.twig` template inside the `my_first_hook` hook. First step is to create a `twig_hooks.yaml` file (or any other format you use) under the `config/packages/` directory (if you don't have one already, of course).

Now, we can define our first hookable with the following configuration:

{% code title="config/packages/twig_hooks.yaml" lineNumbers="true" %}
```yaml
sylius_twig_hooks:
    hooks:
        'my_first_hook':
            some_block:
                template: 'some_block.html.twig'
```
{% endcode %}

Decomposing the example above we can notice that:

1. `sylius_twig_hooks` is the main key for Twig Hooks configuration
2. `hooks` is a configuration key for defining hookables for all hooks
3. `my_first_hook` is our hook name, defined on the Twig file level
4. `some_block` is the name of our hookable, it can be any string, but it should be unique for a given hook unless you want to override the existing hookable (if you want to read more about overriding hookables check the overriding-hookables.md section)
5. finally we have a `template` key that defines which template should be rendered inside the `my_first_hook` hook

**Possible hookable configuration options**

Depending on the hookable template, we can pass different configuration options while defining hookables:

{% tabs %}
{% tab title="Hookable Template" %}
{% code lineNumbers="true" %}
```yaml
sylius_twig_hooks:
    hooks:
        'my_first_hook':
            some_block:
                template: 'some_block.html.twig'
                enabled: true # whether the hookable is enabled
                context: [] # key-value pair that will be passed to the context bag
                configuration: [] # key-value pair that will be passed to the configuration bag
                condition: '@=user != null' # expression evaluated at runtime; the hookable is rendered only when it returns true
                priority: 0 # priority, the higher the number, the earlier the hookable will be hooked
```
{% endcode %}
{% endtab %}

{% tab title="Hookable Component" %}
{% code title="" %}
```yaml
sylius_twig_hooks:
    hooks:
        'my_first_hook':
            some_block:
                component: 'app:block' # component key
                enabled: true # whether the hookable is enabled
                context: [] # key-value pair that will be passed to the context bag
                props: [] # key-value pair that will be passed to our component as props
                configuration: [] # key-value pair that will be passed to the configuration bag
                condition: '@=user != null' # expression evaluated at runtime; the hookable is rendered only when it returns true
                priority: 0 # priority, the higher the number, the earlier the hookable will be hooked
```
{% endcode %}
{% endtab %}
{% endtabs %}

**Conditions in hookables**

Unlike the `enabled` flag — which is evaluated statically during the application boot — the `condition` option is an [Expression Language](https://symfony.com/doc/current/expression_language.html) expression that is evaluated at runtime, each time the hook is rendered. The hookable is rendered only when the expression returns `true`. Both the whole hook context and the `_context` variable are available within the expression, so both of these work:

```yaml
condition: '@=user != null'
condition: '@=_context.user != null'
```

The `user` variable is provided from Symfony's current security token, as in Symfony security expressions. To refer to a `user` value passed explicitly in the hook context, use `_context.user`. For context keys, `_context` behaves like [Twig's `defined`](https://twig.symfony.com/doc/3.x/tests/defined.html)-style check: `@=_context.product` returns `null` when the variable is absent from the context. Referencing a missing variable directly (e.g. `@=product ...`) throws a clear `InvalidExpressionException` instead, which makes invalid conditions fail fast during development.

Conditions can also use Symfony's `is_granted()` authorization check. Pass the required role or attribute, and optionally a subject:

```yaml
condition: '@=is_granted("ROLE_ADMIN")'
condition: '@=is_granted("EDIT", _context.product)'
```

The check uses Symfony's authorization checker, so voters and access decision rules apply as they do in Twig templates.

{% hint style="info" %}
On Symfony 7.1 and newer, conditions are validated at the application boot: an invalid expression is reported as soon as the container is compiled. On older versions, errors are only reported at runtime, when the hook is rendered.
{% endhint %}
