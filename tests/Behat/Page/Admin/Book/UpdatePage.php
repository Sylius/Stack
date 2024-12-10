<?php

namespace MainTests\Sylius\Behat\Page\Admin\Book;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class UpdatePage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'app_admin_book_update';
    }

    public function changeTitle(string $title): void
    {
        $this->getElement('title')->setValue($title);
    }

    protected function getDefinedElements(): array
    {
        return [
            'title' => '#book_title',
        ];
    }
}
