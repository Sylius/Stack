# Passing data to your hookables

One of the most powerful aspects of hooks & hookables is an ability to pass their data down to the children. We can have two sources of the context data:

* Hook-level defined data
* Hookable-level defined data

The context data from these two sources are merged and passed with the metadata to the hookable template or component, so we can use them.

<div data-full-width="false">

<figure><img src="../.gitbook/assets/image (1).png" alt=""><figcaption></figcaption></figure>

</div>

### Example

{% code title="index.html.twig" lineNumbers="true" fullWidth="false" %}
```twig
{#
 # we assume there is a `form` variable holding a `FormView` instance passed
 # from the controller
 #}

<div class="container">
    {{ form_start(form) }}
        {{ form_errors(form) }}
        {{ form_widget(form._token) }}
    
        {% raw %}
{% hook 'index.form' with { form } %}
{% endraw %}
    {{ form_end(form, {render_rest: false}) }}
</div>
```
{% endcode %}

So, as we see at `line 11` we define the `index.form` hook. But also, we pass the `form` with using the `with` keyword. Thanks to it, we are able to pass multiple data to hookables that will hook into the `index.form` hook.

{% hint style="info" %}
`with { form }` is a short-hand for `with { form: form }`, so the key for our `FormView` in the context data bag will be `form.`
{% endhint %}

Now let's create a Twig template rendering some field from our form, and let's make it a hookable.

{% code title="index/some_field.html.twig" lineNumbers="true" %}
```twig
<div class="field">
     {{ form_row(hookable_metadata.context.form.some_field) }}
     
     {# we can also write it like #}
     
     {% raw %}
{% set context = hookable_metadata.context %}
     
     {{ form_row(context.form.some_field) }}
     
     {# or #}
     
     {% set context = get_hookable_context() %}
{% endraw %}
     
     {{ form_row(context.form.some_field) }}
</div>
```
{% endcode %}

{% hint style="info" %}
You can access the context data in multiple ways, so you can pick the one you like the most. Available options are:

* getting it directly from the `hookable_metadata` object like `hookable_metadata.context.<data_key>`
* getting the context data bag via the Twig function like `get_hookable_context().<data_key>`
{% endhint %}

{% hint style="info" %}
Sometimes you might want to override some data that are defined at the hook-level. It is possible by defining the same context data key on the hookable level. If the same context data key is defined at both hook-level and hookable-level the hookable-level one is used.
{% endhint %}
