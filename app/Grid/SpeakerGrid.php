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

use App\Entity\Speaker;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
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

final class SpeakerGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_speaker';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(
                StringFilter::create('search', ['firstName', 'lastName', 'companyName'])
                    ->setLabel('sylius.ui.search'),
            )
            ->addOrderBy('firstName', 'asc')
            ->addField(
                TwigField::create('avatar', 'speaker/grid/field/image.html.twig')
                    ->setPath('.'),
            )
            ->addField(
                StringField::create('firstName')
                    ->setLabel('app.ui.first_name')
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('lastName')
                    ->setLabel('app.ui.last_name')
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('companyName')
                    ->setLabel('app.ui.company_name')
                    ->setSortable(true),
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('show_talks', 'show')
                        ->setIcon('tabler:list-letters')
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
                        ]),
                    UpdateAction::create(),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                    Action::create('redirect_to_speaker_talks', 'redirect_to_speaker_talks')
                        ->setLabel('View talks'),
                ),
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Speaker::class;
    }
}
