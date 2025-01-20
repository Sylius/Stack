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

namespace MainTests\Sylius\Behat\Context\Domain;

use App\Entity\Book;
use App\Factory\BookFactory;
use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Persistence\Proxy;

final class BookContext implements Context
{
    #[Then('the book :title should be added')]
    public function theBookShouldBeAdded(string $title): void
    {
        $exist = false;

        try {
            BookFactory::find(['title' => $title]);
            $exist = true;
        } catch (\RuntimeException) {
        }

        Assert::true($exist);
    }

    /**
     * @param Proxy<Book> $book
     */
    #[Then('/^(this book) title should be "([^"]+)"$/')]
    public function thisBookTitleShouldBe(Proxy $book, string $title): void
    {
        Assert::eq($book->getTitle(), $title);
    }
}
