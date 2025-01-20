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

namespace MainTests\Sylius\Behat\Page\Admin\Book;

use Sylius\BehatBridge\Behat\Page\Admin\Crud\AbstractCreatePage;

final class CreatePage extends AbstractCreatePage
{
    public function getRouteName(): string
    {
        return 'app_admin_book_create';
    }

    public function specifyTitle(string $title): void
    {
        $this->getElement('title')->setValue($title);
    }

    public function specifyAuthor(string $author): void
    {
        $this->getElement('author')->setValue($author);
    }

    protected function getDefinedElements(): array
    {
        return [
            'author' => '#sylius_resource_authorName',
            'title' => '#sylius_resource_title',
        ];
    }
}
