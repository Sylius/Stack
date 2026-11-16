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
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: Talk::class, name: 'app_admin_talk')]
final class TalkGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('startsAt')
            ->setLimits([10, 25, 50])
            ->withFilters(
                EntityFilter::create('conference', Conference::class)
                    ->setLabel('app.ui.conference')
                    ->addFormOption('choice_label', 'name'),
                Filter::create(name: 'speaker', type: SpeakerFilter::class)
                    ->setLabel('app.ui.speaker')
                    ->setOptions(['fields' => ['speakers.id']]),
                DateFilter::create('startsAt')
                    ->setLabel('app.ui.starts_at'),
                SelectFilter::create('track', [
                    'app.ui.biz' => Track::BIZ->value,
                    'app.ui.tech_one' => Track::TECH_ONE->value,
                    'app.ui.tech_two' => Track::TECH_TWO->value,
                ])
                    ->setLabel('app.ui.track'),
            )
            ->withFields(
                TwigField::create('avatar', 'talk/grid/field/images.html.twig')
                    ->setPath('speakers')
                    ->setLabel('app.ui.avatar'),
                TwigField::create('speakers', 'talk/grid/field/speakers.html.twig')
                    ->setLabel('app.ui.speakers'),
                StringField::create('title')
                    ->setLabel('app.ui.title')
                    ->setSortable(true),
                DateTimeField::create('startsAt', 'Y-m-d H:i')
                    ->setLabel('app.ui.starts_at')
                    ->setSortable(true),
            )
            ->withMainActions(
                CreateAction::create(),
            )
            ->withItemActions(
                ShowAction::create()
                    ->setOptions(['link' => ['url' => 'resource.videoUrl']])
                    ->setIcon('tabler:video'),
            )
            ->withSubItemActions(
                UpdateAction::create(),
                DeleteAction::create(),
            )
            ->withBulkActions(
                DeleteAction::create(),
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Talk::class;
    }
}
