# Resource configuration

```txt
src
└── BookStore
    ├── Application
    ├── Domain
    └── Infrastructure
        ├── Sylius
        │   └── Resource
        │       └── BookResource.php
        └── Symfony
            └── Form
                └── BookResourceType.php
```

## Define The Sylius Book Resource

```php
// src/BookStore/Infrastructure/Sylius/Resource/BookResource.php

namespace App\BookStore\Infrastructure\Sylius\Resource;

use App\BookStore\Domain\Model\Book;
use App\BookStore\Infrastructure\Symfony\Form\BookType;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Validator\Constraints as Assert;

#[AsResource(
    section: 'admin',
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/admin',
    driver: false,
)]
final class BookResource implements ResourceInterface
{
    public function __construct(
        public ?AbstractUid $id = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 255, groups: ['create', 'Default'])]
        public ?string $name = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 1023, groups: ['create', 'Default'])]
        public ?string $description = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 255, groups: ['create', 'Default'])]
        public ?string $author = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\Length(min: 1, max: 65535, groups: ['create', 'Default'])]
        public ?string $content = null,

        #[Assert\NotNull(groups: ['create'])]
        #[Assert\PositiveOrZero(groups: ['create', 'Default'])]
        public ?int $price = null,
    ) {
    }

    public function getId(): ?AbstractUid
    {
        return $this->id;
    }

    public static function fromModel(Book $book): self
    {
        return new self(
            $book->id()->value,
            $book->name()->value,
            $book->description()->value,
            $book->author()->value,
            $book->content()->value,
            $book->price()->amount,
        );
    }
}
```

## Define The Symfony Book Resource Form Type

```php
// src/BookStore/Infrastructure/Symfony/Form/BookResourceType.php

namespace App\BookStore\Infrastructure\Symfony\Form;

use App\BookStore\Infrastructure\Sylius\Resource\BookResource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BookResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('author')
            ->add('price')
            ->add('description')
            ->add('content', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookResource::class,
        ]);
    }
}
```
