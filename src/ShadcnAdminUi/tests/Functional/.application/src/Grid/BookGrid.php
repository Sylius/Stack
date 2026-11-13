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

namespace TestApplication\Sylius\ShadcnAdminUi\Grid;

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use TestApplication\Sylius\ShadcnAdminUi\Entity\Book;
use TestApplication\Sylius\ShadcnAdminUi\Enum\BookCategory;

final class BookGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return self::class;
    }

    public function getResourceClass(): string
    {
        return Book::class;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $categories = [];
        foreach (BookCategory::cases() as $category) {
            $categories[ucfirst($category->value)] = $category->value;
        }

        $gridBuilder
            ->orderBy('title')
            ->setLimits([10, 25, 50])
            ->addFilter(StringFilter::create('search', ['title', 'authorName'])->setLabel('sylius.ui.search'))
            ->addFilter(SelectFilter::create('category', $categories)->setLabel('app.ui.category'))
            ->addFilter(BooleanFilter::create('available')->setLabel('app.ui.available'))
            ->addFilter(DateFilter::create('createdAt')->setLabel('app.ui.created_at'))
            ->addField(StringField::create('title')->setLabel('app.ui.title')->setSortable(true))
            ->addField(StringField::create('authorName')->setLabel('app.ui.author_name')->setSortable(true))
            ->addField(TwigField::create('category', 'book/grid/category.html.twig')->setLabel('app.ui.category'))
            ->addField(
                TwigField::create('available', '@SyliusShadcnAdminUi/shared/grid/field/boolean.html.twig')
                    ->setLabel('app.ui.available')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(DateTimeField::create('createdAt', 'd/m/Y')->setLabel('app.ui.created_at')->setSortable(true))
            ->addActionGroup(MainActionGroup::create(CreateAction::create()))
            ->addActionGroup(ItemActionGroup::create(ShowAction::create(), UpdateAction::create(), DeleteAction::create()))
            ->addActionGroup(BulkActionGroup::create(DeleteAction::create()))
        ;
    }
}
