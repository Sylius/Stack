---
title: <div class="field">  {{ for...
---

{% tabs %}
{% tab title="property access via hookable_metadata" %}
<pre class="language-twig" data-title="index/some_field.html.twig" data-line-numbers><code class="lang-twig">&#x3C;div class="field">
  {{ form_row(hookable_metadata.context.form.some_field) }}
<strong> &#x3C;/div>
</strong></code></pre>
{% endtab %}

{% tab title="variable binding" %}
<pre class="language-twig" data-title="index/some_field.html.twig" data-line-numbers><code class="lang-twig">&#x3C;div class="field">
  {% set context = hookable_metadata.context %}
  {{ form_row(context.form.some_field) }}
<strong> &#x3C;/div>
</strong></code></pre>
{% endtab %}

{% tab title="utility function" %}
<pre class="language-twig" data-title="index/some_field.html.twig" data-line-numbers><code class="lang-twig">&#x3C;div class="field">
  {% set context = get_hookable_context() %}
  {{ form_row(context.form.some_field) }}
<strong> &#x3C;/div>
</strong></code></pre>
{% endtab %}
{% endtabs %}
