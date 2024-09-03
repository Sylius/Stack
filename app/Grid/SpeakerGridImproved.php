<?php

namespace App\Grid;

use App\Entity\Speaker;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class SpeakerGridImproved extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_speaker_improved';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(
                StringFilter::create('search', ['firstName', 'lastName', 'companyName'])
                    ->setLabel('sylius.ui.search')
            )
            ->addOrderBy('firstName', 'asc')
            ->addField(
                TwigField::create('avatar', 'speaker/grid/field/image.html.twig')
                    ->setPath('.')
            )
            ->addField(
                StringField::create('firstName')
                    ->setLabel('First Name')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('lastName')
                    ->setLabel('Last Name')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('companyName')
                    ->setLabel('Company Name')
                    ->setSortable(true)
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    Action::create('show_talks', 'show')
                        ->setIcon('list_letters')
                        ->setLabel('app.ui.show_talks')
                        ->setOptions([
                            'link' => [
                                'route' => 'app_admin_talk_index',
                                'parameters' => [
                                    'criteria' => [
                                        'speaker' => 'resource.id',
                                    ],
                                ],
                            ],
                        ])
                    ,
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
        return Speaker::class;
    }
}
