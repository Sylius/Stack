<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Book;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Book>
 */
final class BookFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Book::class;
    }

    public function withTitle(string $title): self
    {
        return $this->with(['title' => $title]);
    }

    public function withAuthorName(string $authorName): self
    {
        return $this->with(['authorName' => $authorName]);
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => ucfirst(self::faker()->words(3, true)),
            'authorName' => self::faker()->firstName() . ' ' . self::faker()->lastName(),
        ];
    }
}
