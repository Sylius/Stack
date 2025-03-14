# Configure your resource

Read the previous chapter to [create a new resource](create_new_resource.md). In order for your resource to truly become a `Sylius Resource`, 
you will need to configure a couple of things.

<!-- TOC -->
* [Configure your resource](#configure-your-resource)
  * [Implement the Resource interface](#implement-the-resource-interface)
  * [Use the Resource attribute](#use-the-resource-attribute)
  * [Advanced configuration](#advanced-configuration)
    * [Configure the resource name](#configure-the-resource-name)
    * [Configure the resource plural name](#configure-the-resource-plural-name)
    * [Configure the resource vars](#configure-the-resource-vars)
<!-- TOC -->

## Implement the Resource interface

First, to declare your resource as a Sylius Resource, implement the ```Sylius\Component\Resource\Model\ResourceInterface```,
which requires defining a getId() method to uniquely identify the resource.

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php

namespace App\Entity;

class Book implements ResourceInterface
{
    public function getId(): int
    {
        return $this->id;
    }
}
```
{% endcode %}

## Use the Resource attribute

Next, add the ```#[AsResource]``` PHP attribute to your Doctrine entity to register it as a Sylius resource.

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
namespace App\Entity;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource]
class Book implements ResourceInterface
{
}

```
{% endcode %}

Run the following command to verify that your resource is correctly configured.

```shell
$ bin/console sylius:debug:resource 'App\Entity\Book'
```

```
+--------------------+------------------------------------------------------------+
| name               | book                                                       |
| application        | app                                                        |
| driver             | doctrine/orm                                               |
| classes.model      | App\Entity\Book                                            |
| classes.controller | Sylius\Bundle\ResourceBundle\Controller\ResourceController |
| classes.factory    | Sylius\Component\Resource\Factory\Factory                  |
| classes.form       | Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType |
+--------------------+------------------------------------------------------------+
```

By default, the alias for your Sylius resource will be `app.book`, which combines the application name and the resource name
with this format : `{application}.{resource}`.

## Advanced configuration

### Configure the resource name

You can override your resource's name via the `name` parameter of the `AsResource` PHP attribute.

{% code title="src/Entity/Order.php" lineNumbers="true" %}
```php
namespace App\Entity;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(name: 'cart')]
class Order implements ResourceInterface
{
}
```
{% endcode %}

In this example, the `order` variable is replaced with `cart` in your Twig templates.
As a result, for the `show` operation, the following Twig variables will be available within the template:

| Name              | Type                                      |
|-------------------|-------------------------------------------|
| resource          | App\Entity\Order                          |
| cart              | App\Entity\Order                          |
| operation         | Sylius\Resource\Metadata\Show             |
| resource_metadata | Sylius\Resource\Metadata\ResourceMetadata |
| app               | Symfony\Bridge\Twig\AppVariable           |

### Configure the resource plural name

You can override your resource's plural name via the `pluralName` parameter of the `AsResource` PHP attribute.

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
namespace App\Entity;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(pluralName: 'library')]
class Book implements ResourceInterface
{
}
```
{% endcode %}

In this example, the `books` variable is replaced with `library` in your Twig templates.
As a result, for the `index` operation, the following Twig variables will be available within the template:

| Name              | Type                                      |
|-------------------|-------------------------------------------|
| resources         | Pagerfanta\Pagerfanta                     |
| library           | Pagerfanta\Pagerfanta                     |
| operation         | Sylius\Resource\Metadata\Index            |
| resource_metadata | Sylius\Resource\Metadata\ResourceMetadata |
| app               | Symfony\Bridge\Twig\AppVariable           |

### Configure additional resource vars

You can define simple variables within the `AsResource` attribute via the `vars` parameter.

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
namespace App\Entity;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(
    vars: [
        'header' => 'Library', 
        'subheader' => 'Managing your library',
    ],
)]
class Book implements ResourceInterface
{
}
```
{% endcode %}

You can then access these variables in your Twig templates.
These variables will be available for every operation associated with this resource.

{% code %}
```html
<h1>{{ operation.vars.header }}</h1>
<h2>{{ operation.vars.subheader }}</h2>
```
{% endcode %}
