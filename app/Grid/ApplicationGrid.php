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

use App\Entity\Application;
use App\Entity\Conference;
use App\Enum\ApplicationStatus;
use App\Grid\Filter\SpeakerFilter;
use Sylius\Bundle\GridBundle\Builder\Action\ApplyTransitionAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EnumFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    resourceClass: Application::class,
    name: 'app_application',
)]
final class ApplicationGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->withFilters(
                EntityFilter::create(name: 'conference', resourceClass: Conference::class, fields: ['talk.conference'])
                    ->setLabel('app.ui.conference')
                    ->addFormOption('choice_label', 'name'),
                Filter::create(name: 'speaker', type: SpeakerFilter::class)
                    ->setLabel('app.ui.speaker')
                    ->setOptions(['fields' => ['talk.speakers.id']]),
                StringFilter::create('search', ['talk.title'])
                    ->setLabel('sylius.ui.search'),
                EnumFilter::create(name: 'status', enumClass: ApplicationStatus::class, field: 'status')
                    ->addFormOption('choice_value', fn (?ApplicationStatus $enum) => $enum?->value)
                    ->addFormOption('choice_label', fn (ApplicationStatus $choice) => ucfirst($choice->value))
                    ->setLabel('app.ui.status'),
                DateFilter::create('submittedAt')
                    ->setLabel('app.ui.submitted_at'),
            )
            ->withFields(
                StringField::create('talk')
                    ->setPath('talk.title')
                    ->setLabel('app.ui.talk'),
                TwigField::create(name: 'speakers', template: 'talk/grid/field/speakers.html.twig')
                    ->setPath('talk.speakers')
                    ->setLabel('app.ui.speakers'),
                StringField::create('status')
                    ->setPath('status.value')
                    ->setLabel('app.ui.status')
                    ->setSortable(true),
                DateTimeField::create('submittedAt')
                    ->setLabel('app.ui.submitted_at')
                    ->setSortable(true),
                DateTimeField::create('updatedAt')
                    ->setLabel('app.ui.updated_at')
                    ->setSortable(true),
            )
            ->withItemActions(
                ApplyTransitionAction::create(name: 'accept', route: 'app_admin_application_accept', options: ['class' => 'outline-success'])
                    ->setIcon('tabler:circle-check')
                    ->setLabel('app.ui.accept'),
                ApplyTransitionAction::create(name: 'reject', route: 'app_admin_application_reject', options: ['class' => 'outline-danger'])
                    ->setIcon('tabler:circle-x')
                    ->setLabel('app.ui.reject'),
                ApplyTransitionAction::create(name: 'hold', route: 'app_admin_application_hold', options: ['class' => 'outline-warning'])
                    ->setIcon('tabler:clock-pause')
                    ->setLabel('app.ui.hold'),
                ApplyTransitionAction::create(name: 'cancel', route: 'app_admin_application_cancel', options: ['class' => 'outline-secondary'])
                    ->setIcon('tabler:ban')
                    ->setLabel('app.ui.cancel'),
            );
    }
}
