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

namespace App\Grid;

use App\Entity\Book;
use App\Enum\BookCategory;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\EnumField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\EnumFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    resourceClass: Book::class,
    name: 'app_book',
)]
final class BookGrid extends AbstractGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->orderBy('title')
            ->withFilters(
                StringFilter::create('search', ['title', 'authorName'])
                    ->setLabel('sylius.ui.search'),
                EnumFilter::create(name: 'category', enumClass: BookCategory::class, field: 'category')
                    ->addFormOption('choice_value', fn (?BookCategory $enum) => $enum?->value)
                    ->addFormOption('choice_label', fn (BookCategory $choice) => ucfirst($choice->value))
                    ->setLabel('app.ui.category'),
            )
            ->withFields(
                StringField::create('title')
                    ->setLabel('app.ui.title')
                    ->setSortable(true),
                StringField::create('authorName')
                    ->setLabel('app.ui.author_name')
                    ->setSortable(true),
                EnumField::create('category')
                    ->setLabel('app.ui.category')
                    ->setSortable(true),
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                    Action::create(name: 'export', type: 'export')
                        ->setOptions([
                            'link' => [
                                'route' => 'app_admin_book_export',
                            ],
                        ]),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                ),
            )
        ;
    }
}
