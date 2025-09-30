# Passing data to your hookables

One of the most powerful aspects of hooks & hookables is the ability to pass data down to their children.
We can have two sources for context data:

* Hook-level defined data
* Hookable-level defined data

Context data from these two sources is merged and passed to the hookable template or component together with the metadata, so we can use them.

<div data-full-width="false">

<figure><img src="../.gitbook/assets/image (1).png" alt=""><figcaption></figcaption></figure>

</div>

### Example

Let's assume we are rendering a form in our `index.html.twig` template via a `form` variable containing a `FormView` instance.
Here, we define an `index.form` hook, and we can pass it the `form`'s context data thanks to the `with` keyword.
This means that we can technically pass down multiple pieces of data to hookables that will hook into `index.form`.

{% code title="index.html.twig" lineNumbers="true" %}
```twig
    <div class="container">
    {% raw %}
        {{ form_start(form) }}  
        {{ form_errors(form) }}
        {{ form_widget(form._token) }}
        
        {% hook 'index.form' with { form } %}
        {{ form_end(form, {render_rest: false}) }}
    {% endraw %}
    </div>
```
{% endcode %}

{% hint style="info" %}
`with { form }` is a short-hand for `with { form: form }`, so the key for our `FormView` in the context data bag will be `form`.
{% endhint %}

Now let's create a Twig template that renders a field from our form and make it hookable. We have 3 possible options : 

{% tabs %}
{% tab title="hookable_metadata.context tag" %}
{% code title="index/some_field.html.twig" lineNumbers="true" %}
```twig
{% raw %}
<div class="field">
     {{ form_row(hookable_metadata.context.form.some_field) }}
</div>
{% endraw %}
```
{% endcode %}
{% endtab %}

{% tab title="local context variable" %}
{% code title="index/some_field.html.twig" lineNumbers="true" %}
```twig
{% raw %}
<div class="field">
    {% set context = hookable_metadata.context %}
     {{ form_row(context.form.some_field) }}
</div>
{% endraw %}
```
{% endcode %}
{% endtab %}

{% tab title="get_hookable_context()" %}
{% code title="index/some_field.html.twig" lineNumbers="true" %}
```twig
{% raw %}
<div class="field">
     {% set context = get_hookable_context() %}
     
     {{ form_row(context.form.some_field) }}
</div>
{% endraw %}
```
{% endcode %}
{% endtab %}
{% endtabs %}

{% hint style="info" %}
You can access the context data in multiple ways, so you can pick the one you like the most. Available options are:

* getting it directly from the `hookable_metadata` object like `hookable_metadata.context.<data_key>`
* getting the context data bag via the Twig function like `get_hookable_context().<data_key>`
{% endhint %}

{% hint style="info" %}
Sometimes you might want to override data that is defined at the hook level. You can do this by defining the same
context data key at the hookable level. If the same context data key is defined at both hook level and hookable level
the hookable level one is used.
{% endhint %}
