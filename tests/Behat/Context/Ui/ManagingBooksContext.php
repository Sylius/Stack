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

namespace MainTests\Sylius\Behat\Context\Ui;

use App\Entity\Book;
use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use MainTests\Sylius\Behat\Page\Admin\Book\CreatePage;
use MainTests\Sylius\Behat\Page\Admin\Book\UpdatePage;
use Zenstruck\Foundry\Persistence\Proxy;

final class ManagingBooksContext implements Context
{
    public function __construct(
        private readonly CreatePage $createPage,
        private readonly UpdatePage $updatePage,
    ) {
    }

    #[When('I want to create a new book')]
    public function iWantToCreateANewBook(): void
    {
        $this->createPage->open();
    }

    /**
     * @param Proxy<Book> $book
     */
    #[When('/^I want to edit (this book)$/')]
    public function iWantToEditThisBook(Proxy $book): void
    {
        $this->updatePage->open(['id' => $book->getId()]);
    }

    #[When('I name it :title')]
    public function iNameIt(string $title): void
    {
        $this->createPage->specifyTitle($title);
    }

    #[When('I specify its author as :author')]
    public function iSpecifyItsAuthorAs(string $author): void
    {
        $this->createPage->specifyAuthor($author);
    }

    #[When('I rename it to :title')]
    public function iRenameItTo(string $title): void
    {
        $this->updatePage->changeTitle($title);
    }

    #[When('I add it')]
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    #[When('I save my changes')]
    public function iSaveMyChanges(): void
    {
        $this->updatePage->update();
    }

    #[Then('the book :title should appear in the list')]
    public function theBookShouldAppearInTheList(string $title): void
    {
        // TODO We need an index page
    }
}
