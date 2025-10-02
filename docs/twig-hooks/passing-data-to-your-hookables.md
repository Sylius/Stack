# Passing data to your hookables

One of the most powerful aspects of hooks & hookables is the ability to pass data down to children elements. We can have two sources of context data:

* Hook-level defined data
* Hookable-level defined data

Context data from these two sources is merged and passed to the **hookable** template or component together with the metadata , so we can access them.

<div data-full-width="false"><figure><img src="../.gitbook/assets/image (1).png" alt=""><figcaption></figcaption></figure></div>

### Example

| <p>Let's assume we want to render a form in our <code>index.html.twig</code> template via a <code>form</code> variable containing a <code>FormView</code> instance.</p><p>Here, we define an <strong><code>index.form</code></strong> hook, and we can pass it the form's context data thanks to the <code>with</code> keyword.</p> |
| ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |

This means that we can technically pass down multiple pieces of data to hookables that will hook into `index.form`.

{% code title="index.html.twig" lineNumbers="true" fullWidth="false" %}
```twig
<div class="container">
    {{ form_start(form) }}
    {{ form_errors(form }}
    {{ form_widget(form._token) }}
    
    {% hook 'index.form' with { form } %}
    
    {{ form_end(form, {render_rest: false} }}
</div>
```
{% endcode %}

{% hint style="info" %}
`with { form }` is a short-hand for `with { form: form }`, so the key for our `FormView` in the context data bag will be `form.`
{% endhint %}

Now let's create a Twig template that renders a field from our form and let's make it a hookable. We have 3 possible options to do this :&#x20;

{% include "../.gitbook/includes/less-than-div-class-field-greater-than-for....md" %}

{% hint style="info" %}
You can access the context data in multiple ways, so you can pick the one you like the most. Available options are:

* getting it directly from the `hookable_metadata` object like `hookable_metadata.context.<data_key>`
* getting the context data bag via the Twig function like `get_hookable_context().<data_key>`
{% endhint %}

### Override behavior

When the same context data key is defined at both the **hook** and **hookable** levels, the **hookable-level** value takes precedence.

{% hint style="info" %}
You can use this to override hook-level data by redefining the key at the hookable level.
{% endhint %}
