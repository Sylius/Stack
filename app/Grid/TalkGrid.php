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

use App\Entity\Conference;
use App\Entity\Talk;
use App\Enum\Track;
use App\Grid\Filter\SpeakerFilter;
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
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class TalkGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_admin_talk';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('startsAt')
            ->setLimits([10, 25, 50])
            ->addFilter(
                EntityFilter::create('conference', Conference::class)
                    ->setLabel('app.ui.conference')
                    ->addFormOption('choice_label', 'name'),
            )
            ->addFilter(
                Filter::create(name: 'speaker', type: SpeakerFilter::class)
                    ->setLabel('app.ui.speaker')
                    ->setFormOptions(['multiple' => true])
                    ->setOptions(['fields' => ['speakers.id']]),
            )
            ->addFilter(
                DateFilter::create('startsAt')
                    ->setLabel('app.ui.starts_at'),
            )
            ->addFilter(
                SelectFilter::create('track', [
                    'app.ui.biz' => Track::BIZ->value,
                    'app.ui.tech_one' => Track::TECH_ONE->value,
                    'app.ui.tech_two' => Track::TECH_TWO->value,
                ])
                    ->setLabel('app.ui.track'),
            )
            ->addField(
                TwigField::create('avatar', 'talk/grid/field/images.html.twig')
                    ->setPath('speakers')
                    ->setLabel('app.ui.avatar'),
            )
            ->addField(
                TwigField::create('speakers', 'talk/grid/field/speakers.html.twig')
                    ->setLabel('app.ui.speakers'),
            )
            ->addField(
                StringField::create('title')
                    ->setLabel('app.ui.title')
                    ->setSortable(true),
            )
            ->addField(
                DateTimeField::create('startsAt', 'Y-m-d H:i')
                    ->setLabel('app.ui.starts_at')
                    ->setSortable(true),
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create()
                        ->setOptions([
                            'link' => [
                                'url' => 'resource.videoUrl',
                            ],
                        ])
                        ->setIcon('tabler:video'),
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

    public function getResourceClass(): string
    {
        return Talk::class;
    }
}
