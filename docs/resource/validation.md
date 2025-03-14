# Validation

<!-- TOC -->
* [on HTML request](#on-html-request)
* [on API request](#on-api-request)
* [Disable validation](#disable-validation)
<!-- TOC -->

It uses `symfony/validator` to validate your data.

## on HTML request

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
namespace App\Entity;

use App\Form\Type\BookType;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Symfony\Component\Validator\Constraints as Assert;

#[AsResource(
    formType: BookType::class, 
    operations: [
        new Create(),
    ],
)
class Book implements ResourceInterface
{
    // ...
    #[Assert\NotBlank()]
    private ?string $title;
}
```
{% endcode %}

In this example, validation will fail when adding a new book without specifying its title on the form.

## on API request

{% code title="src/Entity/Book.php" lineNumbers="true" %}
```php
namespace App\Entity;

use App\Form\Type\BookType;
use Sylius\Resource\Metadata\Api\Post;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Symfony\Component\Validator\Constraints as Assert;

#[AsResource(
    operations: [
        new Post(),
    ],
)
class Book implements ResourceInterface
{
    // ...
    #[Assert\NotBlank()]
    private ?string $title;
}
```
{% endcode %}

In this example, validation will fail when adding a new book without specifying its title on the payload.

## Disable validation

In some case, you may want to disable this validation.

For example, in a "publish" operation, you may want to apply a state machine transition without validation existing data.

{% code title="src/BoardGameBlog/Infrastructure/Sylius/Resource/BoardGameResource.php" lineNumbers="true" %}
```php
namespace App\BoardGameBlog\Infrastructure\Sylius\Resource;

use App\BoardGameBlog\Infrastructure\Sylius\State\Http\Processor\PublishBoardGameProcessor;
use App\BoardGameBlog\Infrastructure\Sylius\State\Http\Provider\BoardGameItemProvider;
use App\BoardGameBlog\Infrastructure\Symfony\Form\Type\BoardGameType;
use Sylius\Resource\Metadata\ApplyStateMachineTransition;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Symfony\Component\Validator\Constraints as Assert;

#[AsResource(
    formType: BoardGameType::class, 
    operations: [
        new Update(
            provider: BoardGameItemProvider::class, 
            processor: PublishBoardGameProcessor::class,
            validate: false, // disable resource validation        
        ),
    ],
)
class BoardGameResource implements ResourceInterface
{
}
```
{% endcode %}
