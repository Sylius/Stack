# Processors

Processors process data: send an email, persist to storage, add to queue etc.

<!-- TOC -->
* [Default processors](#default-processors)
* [Custom processors](#custom-processors)
  * [Example #1: Sending an email after persisting data](#example-1-sending-an-email-after-persisting-data)
  * [Example #2: Use a custom delete processor](#example-2-use-a-custom-delete-processor)
* [Disable processing data](#disable-processing-data)
<!-- TOC -->

## Default processors

When your resource is a Doctrine entity, there are default processors which are already configured to your operations.

As it uses the Doctrine repository configured on your resource, it will automatically flush data for you.

| Operation   | Processor                                              |
|-------------|--------------------------------------------------------|
| create      | Sylius\Resource\Doctrine\Common\State\PersistProcessor |
| update      | Sylius\Resource\Doctrine\Common\State\PersistProcessor |
| delete      | Sylius\Resource\Doctrine\Common\State\RemoveProcessor  |
| bulk delete | Sylius\Resource\Doctrine\Common\State\RemoveProcessor  |

## Custom processors

Custom processors are useful to customize your logic to send an email, persist data to storage, add to queue and for an advanced usage such as an hexagonal architecture.

### Example #1: Sending an email after persisting data

As an example, send an email after customer registration

{% code title="src/Sylius/State/Processor/CreateCustomerProcessor.php" lineNumbers="true" %}
```php
namespace App\Sylius\State\Processor;

use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Resource\Doctrine\Common\State\PersistProcessor;
use Sylius\Resource\State\ProcessorInterface;

final class CreateCustomerProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private PersistProcessor $decorated,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, Customer::class);
        
        $this->decorated->process($data, $operation, $context);

        // Here your logic to send a registration email.
        $this->commandBus->dispatch(new SendRegistrationEmailCommand(new CustomerId($data->id)));

        return null;
    }
}
```
{% endcode %}

Use this processor on your operation.

{% code title="src/Entity/Customer.php" lineNumbers="true" %}
```php

namespace App\Entity\Customer;

use App\Sylius\State\Processor\CreateCustomerProcessor;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(
    operations: [
        new Create(
            processor: CreateCustomerProcessor::class,
        ),
    ],
)
final class BoardGameResource implements ResourceInterface
```
{% endcode %}

### Example #2: Use a custom delete processor

As another example, let's configure a `DeleteBoardGameProcessor` on a `BoardGameResource` which is not a Doctrine entity.

{% code title="src/BoardGameBlog/Infrastructure/Sylius/State/Processor/DeleteBoardGameProcessor.php" lineNumbers="true" %}
```php

namespace App\BoardGameBlog\Infrastructure\Sylius\State\Processor;

final class DeleteBoardGameProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, BoardGameResource::class);

        $this->commandBus->dispatch(new DeleteBoardGameCommand(new BoardGameId($data->id)));

        return null;
    }
}
```
{% endcode %}

Use this processor on your operation.

{% code title="src/BoardGameBlog/Infrastructure/Sylius/Resource/BoardGameResource.php" lineNumbers="true" %}
```php

namespace App\BoardGameBlog\Infrastructure\Sylius\Resource;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(
    section: 'admin',
    formType: BoardGameType::class,
    templatesDir: 'crud',
    routePrefix: '/admin',
    operations: [
        new Delete(
            processor: DeleteBoardGameProcessor::class,
        ),
    ],
)
final class BoardGameResource implements ResourceInterface
```
{% endcode %}

Note that in a delete operation, you can disable providing data.
See [Disable providing data](providers.md#disable-providing-data) chapter.

## Disable processing data

In some cases, you may want not to write data.

For example, you can implement a preview for the updated data without saving them into your storage.

{% code title="src/BoardGameBlog/Infrastructure/Sylius/Resource/BoardGameResource.php" lineNumbers="true" %}
```php

namespace App\BoardGameBlog\Infrastructure\Sylius\Resource;

use App\BoardGameBlog\Infrastructure\Sylius\State\Http\Provider\BoardGameItemProvider;
use App\BoardGameBlog\Infrastructure\Symfony\Form\Type\BoardGameType;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Update;
use Sylius\Resource\Model\ResourceInterface;

#[AsResource(
    section: 'admin',
    formType: BoardGameType::class,
    templatesDir: 'crud',
    routePrefix: '/admin',
    operations: [
        new Update(
            shortName: 'update_preview',
            provider: BoardGameItemProvider::class,
            write: false,   
        ),
    ],
)]
final class BoardGameResource implements ResourceInterface
```
{% endcode %}
