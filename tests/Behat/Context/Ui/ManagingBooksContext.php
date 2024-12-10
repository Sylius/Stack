<?php

declare(strict_types=1);

namespace MainTests\Sylius\Behat\Context\Ui;

use App\Entity\Book;
use Behat\Behat\Context\Context;
use Behat\Step\When;
use MainTests\Sylius\Behat\Page\Admin\Book\UpdatePage;
use Zenstruck\Foundry\Persistence\Proxy;

final class ManagingBooksContext implements Context
{
    public function __construct(
        private readonly UpdatePage $updatePage,
    ) {
    }

    /**
     * @param Proxy<Book> $book
     */
    #[When('/^I want to edit (this book)$/')]
    public function iWantToEditThisBook(Proxy $book): void
    {
        $this->updatePage->open(['id' => $book->getId()]);
    }
}
