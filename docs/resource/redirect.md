# Redirect

After that an action has been performed, the operation can be redirected to another operation.

<!-- TOC -->
* [Default redirections](#default-redirections)
* [Custom redirection](#custom-redirection)
* [Pass arguments to your redirection](#pass-arguments-to-your-redirection)
<!-- TOC -->


## Default redirections

Redirections are configured on your operations with these default behaviours.

| Operation   | Redirection                         |
|-------------|-------------------------------------|
| create      | `show` if exists, otherwise `index` |
| update      | `show` if exists, otherwise `index` |  
| delete      | `index`                             |
| bulk_delete | `index`                             |

## Custom redirection

For example, let's configure a custom redirection to create & update operations.

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php

declare(strict_types=1);

namespace App\Entity;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Update;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(
    operations: [
        new Create(
            redirectToRoute: 'app_book_update',
        ),
        new Update(
            redirectToRoute: 'app_book_update',
        ),
    ],
)
class Book implements ResourceInterface
{
}
```
{% endcode %}

After adding or editing a book, it will be redirected to the edition page of a book.

## Pass arguments to your redirection

You can pass arguments to your redirection method.

3 variables are available:

* `resource`: to retrieve data from the instantiated resource
* `{name_of_your_resource}`: If your resource is a book instance, it will be also available as `book` variable
* `request`: to retrieve data from the request via Symfony\Component\HttpFoundation\Request

It uses the [Symfony expression language](https://symfony.com/doc/current/components/expression_language.html) component.

As an example, let's redirect a book creation to the author details page of the created book.

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php

declare(strict_types=1);

namespace App\Entity;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(
    operations: [
        new Create(
            redirectToRoute: 'app_author_show', 
            # You can use either the generic resource variable
            redirectArguments: ['id' => 'resource.getAuthor().getId()']
            # Or you can use the resource name
            redirectArguments: ['id' => 'book.getAuthor().getId()']
        ),
    ],
)
class Book implements ResourceInterface
{
}
```
{% endcode %}
