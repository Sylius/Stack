---
description: >-
  Twig Extra is a set of Twig extensions that provides additional Twig helpers.
---

# Getting started

## Installation

Install the package using Composer and Symfony Flex:

```bash
composer require sylius/twig-extra
```

## Features

### Sort by

This extension allows you to sort an array of objects by a specific property.

```php
class Book {
    public function __construct() {
        public string $name,
    }
}

$books = [
    new Book('The Shining'), 
    new Book('The Lord Of The Rings'), 
    new Book('Dune'),
    new Book('Wuthering Heights'),
    new Book('Fahrenheit 451'),
     ];
```

```twig
{% raw %}
<ul>
{% for book in books|sort_by('name') %}
    <li>{{ book.name }}</li>
{% endif %}
</ul>
{% endraw %}
```

```text
. Dune
. Fahrenheit 451
. The Lord Of The Rings
. The Shining
. Wuthering Heights
```

You can also sort nested arrays.

```php

$books = [
    ['name' => 'The Shining'], 
    ['name' => 'The Lord Of The Rings'],
    ['name' => 'Dune'],
    ['name' => 'Wuthering Heights'],
    ['name' => 'Fahrenheit 451'],
];
```

You just need to encapsulate the key with `[]`.

```twig
{% raw %}
<ul>
{% for book in books|sort_by('[name]') %}
    <li>{{ book.name }}</li>
{% endif %}
</ul>
{% endraw %}
```

### Test HTML attribute

This Twig extension lets you add data attributes in a test environment or when debug mode is enabled.
This makes it easy to identify your data in E2E tests while minimizing dependency on HTML changes.

```twig
<h1 {{ sylius_test_html_attribute('title')>The Shining</h1>
```

```html
<h1 data-test-title>The Shining</h1>
```

### Test Form HTML attribute

Like the `sylius_test_html_attribute` Twig extension, this one allows you to add some data attributes in your test environment or when debug mode is enabled.
This function adds the data attribute via the `attr` Twig variable on a form theme block.

```twig
{{ form_row(form.title, sylius_test_form_attribute('title')) }}
```

```html
<!-- Actual html output below depends on your form theme -->
<label for="book_title">Title</label>
<input 
        type="text" 
        id="book_title" 
        name="title" 
        data-test-title <!-- This is the added data attribute -->
/>
```
