# Creating Resources

{% hint style="warning" %}
This section is deprecated. However, as of now, the Sylius E-Commerce project is still resorting to this configuration so you might want to check it out.
{% endhint %}

To display a form, handle its submission or to create a new resource via API,
you should use the **createAction** of your **app.controller.book** service.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml

app_book_create:
    path: /books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
```
{% endcode %}

Done! Now when you go to ``/books/new``, the ResourceController will use the factory (``app.factory.book``) to create a new book instance.
Then it will try to create an ``app_book`` form, and set the newly created book as its data.

## Submitting the Form

You can use exactly the same route to handle the submit of the form and create the book.

```html
    <form method="post" action="{{ path('app_book_create') }}">
```
On submit, the create action with method POST, will bind the request on the form, and if it is valid it will use the right manager to persist the resource.
Then, by default it redirects to ``app_book_show`` to display the created book, but you can easily change that behavior - you'll see this in further sections.

When validation fails, it will render the form just like previously with the error messages displayed.

## Changing the Template

Just like for the **show** and **index** actions, you can customize the template per route.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            template: Book/create.html.twig
```
{% endcode %}

## Using Custom Form


You can also use custom form type on per route basis. Following Symfony3 conventions [forms types](http://symfony.com/doc/current/forms.html#building-the-form) are resolved by FQCN.
Below you can see the usage for specifying a custom form.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            form: App\Form\BookType
```
{% endcode %}

## Passing Custom Options to Form

What happens when you need pass some options to the form?
Well, there's a configuration for that!

Below you can see the usage for specifying custom options, in this case, ``validation_groups``, but you can pass any option accepted by the form.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            form:
                type: App\Form\BookType
                options:
                    validation_groups: [sylius, my_custom_group]
```
{% endcode %}

## Using Custom Factory Method

By default, ``ResourceController`` will use the ``createNew`` method with no arguments to create a new instance of your object. However, this behavior can be modified.
To use a different method of your factory, you can simply configure the ``factory`` option.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            factory: createNewWithAuthor
```
{% endcode %}

Additionally, if you want to provide your custom method with arguments from the request, you can do so by adding more parameters.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /books/{author}/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            factory:
                method: createNewWithAuthor
                arguments: [$author]
```
{% endcode %}

With this configuration, ``$factory->createNewWithAuthor($request->get('author'))`` will be called to create new resource within the ``createAction``.

## Using Custom Factory Service

If you would like to use your own service to create the resource, then try the following configuration:

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /{authorId}/books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            factory:
                method: ["expr:service('app.factory.custom_book_factory')", "createNewByAuthorId"]
                arguments: $authorId
```
{% endcode %}

With this configuration, service with id "app.factory.custom_book_factory" will be called to create new resource within the ``createNewByAuthorId`` method and the author id from the url as argument.

## Custom Redirect After Success

By default the controller will try to get the id of the newly created resource and redirect to the "show" route.
You can easily change that behaviour.
For example, to redirect to the index list after successfully creating a new resource - you can use the following configuration.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            redirect: app_book_index
```
{% endcode %}

You can also perform more complex redirects, with parameters. For example:

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /genre/{genreId}/books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            redirect:
                route: app_genre_show
                parameters: { id: $genreId }
```
{% endcode %}

In addition to the request parameters, you can access some of the newly created objects properties, using the ``resource.`` prefix.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_create:
    path: /books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            redirect:
                route: app_book_show
                parameters: { title: resource.title }
```
{% endcode %}

With this configuration, the ``title`` parameter for route ``app_book_show`` will be obtained from your newly created book.

## Custom Event Name

By default, there are two events dispatched during resource creation, one before adding it do database, the other after successful addition.
The pattern is always the same - ``{applicationName}.{resourceName}.pre/post_create``. However, you can customize the last part of the event, to provide your
own action name.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book_customer_create:
    path: /customer/books/new
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            event: customer_create
```
{% endcode %}

This way, you can listen to ``app.book.pre_customer_create`` and ``app.book.post_customer_create`` events. It's especially useful, when you use
``ResourceController:createAction`` in more than one route.

## Configuration Reference

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_genre_book_add:
    path: /{genreName}/books/add
    methods: [GET, POST]
    defaults:
        _controller: app.controller.book::createAction
        _sylius:
            template: Book/addToGenre.html.twig
            form: App\Form\BookType
            event: book_create
            factory:
                method: createForGenre
                arguments: [$genreName]
            criteria:
                group.name: $genreName
            redirect:
                route: app_book_show
                parameters: { title: resource.title }
```
{% endcode %}

Remember that you can use controller's Fully Qualified Class Name (``App\Controller\BookController``) instead of id ``app.controller.book`` 

**[Go back to the documentation's index](index.md)**
