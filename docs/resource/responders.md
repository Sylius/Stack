# Responders

Responders respond data: transform data to a Symfony response, return a success in a CLI operation.

<!-- TOC -->
* [Default responders](#default-responders)
* [Twig Responder](#twig-responder)
  * [Customize Twig template variables](#customize-twig-template-variables)
* [API Responder](#api-responder)
<!-- TOC -->

## Default responders

When your operation is an instance of `Sylius\Component\Resource\Metadata\HttpOperation` two responders are configured by default.

The responder will automatically choose the responder depending on the request format:

| Request format | Responder                                                     |
|----------------|---------------------------------------------------------------|
| html           | Sylius\Component\Resource\Symfony\Request\State\TwigResponder |
| json           | Sylius\Component\Resource\Symfony\Request\State\ApiResponder  |
| xml            | Sylius\Component\Resource\Doctrine\Common\State\ApiResponder  |


## Twig Responder

The Twig responder is used to render data into a Symfony response.
It's used for HTML responses.

The variables that are passed to the Twig templates depends on the operation (See [Configure your operations](configure_your_operations.md) chapter).

### Customize Twig template variables

Some variables are already available on your operations, but you can add more variables easily.

As an example, we add a `foo` variable to the Twig template with `bar` as value.

{% code title="src/Twig/Context/Factory/ShowSubscriptionContextFactory.php" lineNumbers="true" %}
```php

namespace App\Twig\Context\Factory;

use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\Twig\Context\Factory\ContextFactoryInterface;

final class ShowSubscriptionContextFactory implements ContextFactoryInterface
{
    public function __construct(private ContextFactoryInterface $decorated)
    {
    }

    public function create(mixed $data, Operation $operation, Context $context): array
    {
        return array_merge($this->decorated->create($data, $operation, $context), [
            'foo' => 'bar',
        ]);
    }
}
```
{% endcode %}

Use it on your operation.

{% code title="src/Entity/Subscription.php" lineNumbers="true" %}
```php

namespace App\Entity;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(
    operations: [
        new Show(
            template: 'subscription/show.html.twig',
            twigContextFactory: ShowSubscriptionContextFactory::class,      
        ),
    ],
)
class Subscription implements ResourceInterface
{
}
```
{% endcode %}

## API Responder

The API responder is used to render serialized data into a Symfony response.
It's used for JSON/XML responses.
