<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use \Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @template TModel of Book
 * @template-extends PersistentObjectFactory<TModel>
 *
 * @method static Book|Proxy createOne(array $attributes = [])
 * @method static Book[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Book[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Book|Proxy find(object|array|mixed $criteria)
 * @method static Book|Proxy findOrCreate(array $attributes)
 * @method static Book|Proxy first(string $sortedField = 'id')
 * @method static Book|Proxy last(string $sortedField = 'id')
 * @method static Book|Proxy random(array $attributes = [])
 * @method static Book|Proxy randomOrCreate(array $attributes = [])
 * @method static Book[]|Proxy[] all()
 * @method static Book[]|Proxy[] findBy(array $attributes)
 * @method static Book[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Book[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static BookRepository|ProxyRepositoryDecorator repository()
 * @method Book|Proxy create(array|callable $attributes = [])
 */
final class BookFactory extends PersistentProxyObjectFactory
{
    public function withTitle(string $title): self
    {
        return $this->with(['title' => $title]);
    }

    public function withAuthorName(string $authorName): self
    {
        return $this->with(['authorName' => $authorName]);
    }

    public static function class(): string
    {
        return Book::class;
    }

    /**
     * @return array<string, mixed>|callable
     */
    protected function defaults(): array|callable
    {
        return [
            'title' => ucfirst(self::faker()->words(3, true)),
            'authorName' => self::faker()->firstName() . ' ' . self::faker()->lastName(),
        ];
    }
}
