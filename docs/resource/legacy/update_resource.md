# Updating Resources

{% hint style="warning" %}
This section is deprecated. However, as of now, the Sylius E-Commerce project is still resorting to this configuration so you might want to check it out.
{% endhint %}

To display an edit form of a particular resource, change it or update it via API, you should use the **updateAction** action of your **app.controller.book** service.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /books/{id}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/books/{id}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
)]
```
{% endcode %}

</details>

Done! Now when you go to ``/books/5/edit``, ResourceController will use the repository (``app.repository.book``) to find the book with id == **5**.
If found it will create the ``app_book`` form, and set the existing book as data.

## Submitting the Form

You can use exactly the same route to handle the submit of the form and updating the book.

```html
<form method="post" action="{{ path('app_book_update', {'id': book.id}) }}">
    <input type="hidden" name="_method" value="PUT" />
```
On submit, the update action with method PUT, will bind the request on the form, and if it is valid it will use the right manager to persist the resource.
Then, by default it redirects to ``app_book_show`` to display the updated book, but like for creation of the resource - it's customizable.

When validation fails, it will simply render the form again, but with error messages.

## Changing the Template

Just like for other actions, you can customize the template.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /books/{id}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            template: Admin/Book/update.html.twig
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/books/{id}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    template: 'Admin/book/update.html.twig',
)]
```
{% endcode %}

</details>

## Using Custom Form

Same way like for **createAction** you can override the default form.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /books/{id}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            form: App\Form\BookType
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\BookType;
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/books/{id}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    form: BookType::class,
)]
```
{% endcode %}

</details>

## Passing Custom Options to Form

Same way like for **createAction** you can pass options to the form.

Below you can see how to specify custom options, in this case, ``validation_groups``, but you can pass any option accepted by the form.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /books/{id}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            form:
                type: app_book_custom
                options:
                    validation_groups: [sylius, my_custom_group]
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\BookType;
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/books/{id}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    form: [
        'type' => BookType::class,
        'validation_groups' => ['sylius', 'my_custom_group'],
    ],
)]
```
{% endcode %}

</details>

## Overriding the Criteria

By default, the **updateAction** will look for the resource by id. You can easily change that criteria.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /books/{title}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            criteria: { title: $title }
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\BookType;
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/books/{id}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    criteria: [
        'title' => '$title',
    ],
)]
```
{% endcode %}

</details>

## Custom Redirect After Success

By default the controller will try to get the id of resource and redirect to the "show" route. To change that, use the following configuration.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /books/{id}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            redirect: app_book_index
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\BookType;
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/books/{id}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    redirect: 'app_book_index',
)]
```
{% endcode %}

</details>

You can also perform more complex redirects, with parameters. For example:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /genre/{genreId}/books/{id}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            redirect:
                route: app_genre_show
                parameters: { id: $genreId }
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\BookType;
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/genre/{genreId}/books/{id}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    redirect: [
        'route' => 'app_genre_show',
        'parameters' => ['id' => '$genreId'],
    ],
)]
```
{% endcode %}

</details>

## Custom Event Name

By default, there are two events dispatched during resource update, one before setting new data, the other after successful update.
The pattern is always the same - ``{applicationName}.{resourceName}.pre/post_update``. However, you can customize the last part of the event, to provide your
own action name.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_customer_update:
    path: /customer/book-update/{id}
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            event: customer_update
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\BookType;
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_customer_update',
    path: '/customer/book-update/{id}',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    event: 'customer_update',
)]
```
{% endcode %}

</details>

This way, you can listen to ``app.book.pre_customer_update`` and ``app.book.post_customer_update`` events. It's especially useful, when you use
``ResourceController:updateAction`` in more than one route.


## [API] Returning resource or no content

Depending on your app approach it can be useful to return a changed object or only the ``204 HTTP Code``, which indicates that everything worked smoothly.
Sylius, by default is returning the ``204 HTTP Code``, which indicates an empty response. If you would like to receive a whole object as a response you should set a ``return_content`` option to true.

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /books/{title}/edit
    methods: [GET, PUT]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            criteria: { title: $title }
            return_content: true
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\BookType;
use Sylius\Resource\Annotation\SyliusRoute;

#[SyliusRoute(
    name: 'app_book_update',
    path: '/books/{title}/edit',
    methods: ['GET', 'PUT'],
    controller: 'app.controller.book::updateAction',
    criteria: ['title' => '$title'],
    returnContent: true,
)]
```
{% endcode %}

</details>

### **Warning**
The ``return_content`` flag is available for the ``applyStateMachineTransitionAction`` method as well. But these are the only ones which can be configured this way.
It is worth noticing, that the ``applyStateMachineTransitionAction`` returns a default ``200 HTTP Code`` response with a fully serialized object.

## Configuration Reference

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_update:
    path: /genre/{genreId}/books/{title}/edit
    methods: [GET, PUT, PATCH]
    defaults:
        _controller: app.controller.book::updateAction
        _sylius:
            template: Book/editInGenre.html.twig
            form: app_book_custom
            event: book_update
            repository:
                method: findBookByTitle
                arguments: [$title, expr:service('app.context.book')]
            criteria:
                enabled: true
                genreId: $genreId
            redirect:
                route: app_book_show
                parameters: { title: resource.title }
            return_content: true
```     
{% endcode %}

Remember that you can use controller's Fully Qualified Class Name (``App\Controller\BookController``) instead of id ``app.controller.book`` 
     
**[Go back to the documentation's index](index.md)**

