<?php

namespace App\Grid;

use App\Entity\Speaker;
use App\Entity\Talk;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class TalkGridImproved extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_talk_improved';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(
                StringFilter::create('search', ['title', 'speaker.firstName', 'speaker.lastName'])
                    ->setLabel('sylius.ui.search')
            )
            ->addFilter(
                EntityFilter::create('speaker', Speaker::class)
                    ->setLabel('app.ui.speaker')
                    ->addFormOption('choice_label',  'fullName')
            )
            ->addOrderBy('title')
            ->addField(
                TwigField::create('avatar', 'speaker/grid/field/image.html.twig')
                    ->setPath('speaker')
            )
            ->addField(
                StringField::create('title')
                    ->setLabel('Title')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('speaker')
                    ->setLabel('app.ui.speaker')
                    ->setPath('speaker.fullName')
                    ->setSortable(true, 'speaker.firstName')
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()
                )
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Talk::class;
    }
}
