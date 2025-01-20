<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MainTests\Sylius\Behat\Context\Setup;

use App\Factory\BookFactory;
use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\BehatBridge\Storage\SharedStorageInterface;

final class BookContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
    ) {
    }

    #[Given('there is a book :title')]
    public function thereIsABook(string $title): void
    {
        $book = BookFactory::new()
            ->withTitle($title)->create()
        ;

        $this->sharedStorage->set('book', $book);
    }
}
