---
description: >-
  Twig Extra is a set of Twig extensions to bring more Twig helpers.
---

# Getting started

## Installation

Install the package using Composer and Symfony Flex:

```bash
composer require sylius/twig-extra
```

## Features

### Sort by

This sort by extension allows to sort an array of objects by a specific property.

```php
class Book {
    public function __construct() {
        public string $name,
    }
}

$books = [new Book('Shinning'), new Book('A Lord Of The Rings')];
```

```twig
<ul>
{% for book in books|sort_by('name') %}
    <li>{{ book.name }}</li>
{% endif %}
</ul>
```

```text
. A Lord Of The Rings
. Shinning
```

You can also sort array of arrays.

```php

$books = [['name' => 'Shinning'], ['name' => 'A Lord Of The Rings']];
```

You just need to encapsulate the key with `[]`.

```twig
<ul>
{% for book in books|sort_by('[name]') %}
    <li>{{ book.name }}</li>
{% endif %}
</ul>
```

### Test HTML attribute

This Twig extension allows you to add some data attributes in test environment or when debug is enabled.
This allows to identify your data easily in E2E tests without being too much dependant of your HTML changes.

```twig
<h1 {{ sylius_test_html_attribute('title')>Shinning</h1>
```

```html
<h1 data-test-title>Shinning</h1>
```

### Test Form HTML attribute

Like the `sylius_test_html_attribute` Twig extension, this one allows you to add some data attributes in test environment or when debug is enabled.
This function adds the data attribute via the `attr` Twig variable on a form theme block.

```twig
{{ form_row(form.title, sylius_test_form_attribute('title')) }}
```

```html
<!-- Actual html output bellow depends on your form theme -->
<label for="book_title">Title</label>
<input 
        type="text" 
        id="book_title" 
        name="title" 
        data-test-title <!-- This is the added data attribute -->
/>
```
