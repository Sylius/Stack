# Routing

{% hint style="warning" %}
This section is deprecated. However, as of now, the Sylius E-Commerce project is still resorting to this configuration so you might want to check it out.
{% endhint %}

SyliusResourceBundle ships with a custom route loader that can save you some time.

## Generating Generic CRUD Routing

To generate a full CRUD routing, simply configure it in your ``config/routes.yaml``:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
)]
```
{% endcode %}

</details>

Results in the following routes:

```bash
php bin/console debug:router
```
```
------------------------ --------------- -------- ------ -------------------------
Name                     Method          Scheme   Host   Path
------------------------ --------------- -------- ------ -------------------------
app_book_index           GET             ANY      ANY    /books/
app_book_create          GET|POST        ANY      ANY    /books/new
app_book_update          GET|PUT|PATCH   ANY      ANY    /books/{id}/edit
app_book_show            GET             ANY      ANY    /books/{id}
app_book_bulk_delete     DELETE          ANY      ANY    /books/bulk-delete
app_book_delete          DELETE          ANY      ANY    /books/{id}
```
## Using a Custom Path

By default, Sylius will use a plural form of the resource name, but you can easily customize the path:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        path: library
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    path: 'library',
)]
```
{% endcode %}

</details>

Results in the following routes:

```bash
php bin/console debug:router
```
```
------------------------ --------------- -------- ------ -------------------------
Name                     Method          Scheme   Host   Path
------------------------ --------------- -------- ------ -------------------------
app_book_index           GET             ANY      ANY    /library/
app_book_create          GET|POST        ANY      ANY    /library/new
app_book_update          GET|PUT|PATCH   ANY      ANY    /library/{id}/edit
app_book_show            GET             ANY      ANY    /library/{id}
app_book_bulk_delete     DELETE          ANY      ANY    /library/bulk-delete
app_book_delete          DELETE          ANY      ANY    /library/{id}
```
## Generating API CRUD Routing

To generate a full API-friendly CRUD routing, add these YAML lines to your ``config/routes.yaml``:

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
    type: sylius.resource_api
```
{% endcode %}

Results in the following routes:

```bash
php bin/console debug:router
```
```
------------------------ --------------- -------- ------ -------------------------
Name                     Method          Scheme   Host   Path
------------------------ --------------- -------- ------ -------------------------
app_book_show            GET             ANY      ANY    /books/{id}
app_book_index           GET             ANY      ANY    /books/
app_book_create          POST            ANY      ANY    /books/
app_book_update          PUT|PATCH       ANY      ANY    /books/{id}
app_book_delete          DELETE          ANY      ANY    /books/{id}
```
## Excluding Routes

If you want to skip some routes, simply use ``except`` configuration:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        except: ['delete', 'update']
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    except: ['delete', 'update'],
)]
```
{% endcode %}

</details>

Results in the following routes:

```bash
php bin/console debug:router
```
```
------------------------ --------------- -------- ------ -------------------------
Name                     Method          Scheme   Host   Path
------------------------ --------------- -------- ------ -------------------------
app_book_index           GET             ANY      ANY    /books/
app_book_create          GET|POST        ANY      ANY    /books/new
app_book_show            GET             ANY      ANY    /books/{id}
app_book_bulk_delete     DELETE          ANY      ANY    /books/bulk-delete
```
## Generating Only Specific Routes

If you want to generate only some specific routes, simply use the ``only`` configuration:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        only: ['show', 'index']
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    only: ['show', 'index'],
)]
```
{% endcode %}

</details>

Results in the following routes:

```bash
php bin/console debug:router
```
```
------------------------ --------------- -------- ------ -------------------------
Name                     Method          Scheme   Host   Path
------------------------ --------------- -------- ------ -------------------------
app_book_index           GET             ANY      ANY    /books/
app_book_show            GET             ANY      ANY    /books/{id}
```
## Generating Routing for a Section

Sometimes you want to generate routing for different "sections" of an application:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_admin_book:
    resource: |
        alias: app.book
        section: admin
    type: sylius.resource
    prefix: /admin

app_library_book:
    resource: |
        alias: app.book
        section: library
        only: ['show', 'index']
    type: sylius.resource
    prefix: /library
```
{% endcode %}

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    path: '/admin/books',
    section: 'admin',
)]

#[SyliusCrudRoutes(
    alias: 'app.book',
    path: '/library/books',
    section: 'library',
    only: ['show', 'index'],
)]
```
{% endcode %}

</details>

The generation results in the following routes:

```bash
php bin/console debug:router
```
```
-------------------------- --------------- -------- ------ -------------------------
Name                        Method          Scheme   Host   Path
-------------------------- --------------- -------- ------ -------------------------
app_admin_book_index        GET             ANY      ANY    /admin/books/
app_admin_book_create       GET|POST        ANY      ANY    /admin/books/new
app_admin_book_update       GET|PUT|PATCH   ANY      ANY    /admin/books/{id}/edit
app_admin_book_show         GET             ANY      ANY    /admin/books/{id}
app_admin_book_bulk_delete  DELETE          ANY      ANY    /admin/books/bulk-delete
app_admin_book_delete       DELETE          ANY      ANY    /admin/books/{id}
app_library_book_show       GET             ANY      ANY    /library/books/{id}
app_library_book_index      GET             ANY      ANY    /library/books/
```
## Using Custom Templates

By default, ``ResourceController`` will use the templates namespace you have configured for the resource.
You can easily change that per route, but it is also easy when you generate the routing:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_admin_book:
    resource: |
        alias: app.book
        section: admin
        templates: Admin/Book
    type: sylius.resource
    prefix: /admin
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    path: '/admin/books',
    section: 'admin',
    templates: 'Admin/Book',
)]
```
{% endcode %}

</details>

Following templates will be used for actions:

* ``:templates/Admin/Book:show.html.twig``
* ``:templates/Admin/Book:index.html.twig``
* ``:templates/Admin/Book:create.html.twig``
* ``:templates/Admin/Book:update.html.twig``

## Using a Custom Form

If you want to use a custom form:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        form: App/Form/Type/AdminBookType
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use App\Form\Type\AdminBookType;
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    form: AdminBookType::class,
)]
```
{% endcode %}

</details>

``create`` and ``update`` actions will use ``App/Form/Type/AdminBookType`` form type.

### **Note** 
Remember, that if your form type has some dependencies you have to declare it as a service and tag with **name: form.type**. You can read more about it [here]( http://docs.sylius.com/en/latest/components_and_bundles/bundles/SyliusResourceBundle/forms.html#custom-resource-form)

## Using a Custom Redirect

By default, after successful resource creation or update, Sylius will redirect to the ``show`` route and fallback to ``index`` if it does not exist.
If you want to change that behavior, use the following configuration:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        redirect: update
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    redirect: 'update',
)]
```
{% endcode %}

</details>

## API Versioning

One of the ResourceBundle dependencies is JMSSerializer, which provides a useful functionality of [object versioning](http://jmsyst.com/libs/serializer/master/cookbook/exclusion_strategies#versioning-objects). It is possible to take an advantage of it almost out of the box.
If you would like to return only the second version of your object serializations, use the following snippet:

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        serialization_version: 2
    type: sylius.resource_api
```
{% endcode %}

What is more, you can use a path variable to dynamically change your request. You can achieve this by setting a path prefix when importing file or specify it in the path option.

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        serialization_version: $version
    type: sylius.resource_api
```
{% endcode %}

### **Note** 
Remember that a dynamically resolved `books` prefix is no longer available when you specify ``path``, and it has to be defined manually.

## Using a Custom Criteria

Sometimes it is convenient to add some additional constraint when resolving resources. For example, one could want to present a list of all books from some library (which id would be a part of path).
Assuming that the path prefix is `/libraries/{libraryId}`, if you would like to list all books from this library, you could use the following snippet:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        alias: app.book
        path: '/libraries/{libraryId}/books'
        criteria:
            library: $libraryId
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    alias: 'app.book',
    path: '/library/{libraryId}/books',
    criteria: [
        'library' => '$libraryId',
    ],
)]
```
{% endcode %}

</details>

Which will result in the following routes:

```bash
php bin/console debug:router
```
```
------------------------ --------------- -------- ------ ---------------------------------------
Name                     Method          Scheme   Host   Path
------------------------ --------------- -------- ------ ---------------------------------------
app_book_index           GET             ANY      ANY    /libraries/{libraryId}/books/
app_book_create          GET|POST        ANY      ANY    /libraries/{libraryId}/books/new
app_book_update          GET|PUT|PATCH   ANY      ANY    /libraries/{libraryId}/books/{id}/edit
app_book_show            GET             ANY      ANY    /libraries/{libraryId}/books/{id}
app_book_bulk_delete     DELETE          ANY      ANY    /libraries/{libraryId}/books/bulk-delete
app_book_delete          DELETE          ANY      ANY    /libraries/{libraryId}/books/{id}
```

## Using a Custom Identifier

As you could notice the generated routing resolves resources by the ``id`` field. But sometimes it is more convenient to use a custom identifier field instead, let's say a ``code`` (or any other field of your choice which can uniquely identify your resource).
If you want to look for books by ``isbn``, use the following configuration:

<details open><summary>Yaml</summary>

{% code title="config/routes.yaml" lineNumbers="true" %}
```yaml
app_book:
    resource: |
        identifier: isbn
        alias: app.book
        criteria:
            isbn: $isbn
    type: sylius.resource
```
{% endcode %}

</details>

<details open><summary>PHP</summary>

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
use Sylius\Resource\Annotation\SyliusCrudRoutes;

#[SyliusCrudRoutes(
    identifier: 'isbn',
    alias: 'app.book',
    criteria: [
        'isbn' => '$isbn',
    ],
)]
```
{% endcode %}

</details>

Which will result in the following routes:

```bash
php bin/console debug:router
```
```
------------------------ --------------- -------- ------ -------------------------
Name                     Method          Scheme   Host   Path
------------------------ --------------- -------- ------ -------------------------
app_book_index           GET             ANY      ANY    /books/
app_book_create          GET|POST        ANY      ANY    /books/new
app_book_update          GET|PUT|PATCH   ANY      ANY    /books/{isbn}/edit
app_book_show            GET             ANY      ANY    /books/{isbn}
app_book_bulk_delete     DELETE          ANY      ANY    /books/bulk-delete
app_book_delete          DELETE          ANY      ANY    /books/{isbn}
```

**[Go back to the documentation's index](index.md)**
