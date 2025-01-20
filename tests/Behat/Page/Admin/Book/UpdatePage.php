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

use Sylius\BehatBridge\Behat\Page\Admin\Crud\AbstractUpdatePage;

final class UpdatePage extends AbstractUpdatePage
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
            'title' => '#sylius_resource_title',
        ];
    }
}
