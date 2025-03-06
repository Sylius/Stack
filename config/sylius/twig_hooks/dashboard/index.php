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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Twig\Component\NewSpeakersComponent;
use App\Twig\Component\NewTalksComponent;
use App\Twig\Component\StatisticsComponent;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_hooks', [
        'hooks' => [
            'sylius_admin.dashboard.index.content' => [
                'statistics' => [
                    'component' => StatisticsComponent::class,
                ],
                'last_updates' => [
                    'template' => 'dashboard/index/content/last_updates.html.twig',
                ],
            ],

            'sylius_admin.dashboard.index.content.statistics' => [
                'statistics_grid' => [
                    'template' => 'dashboard/index/content/statistics_grid.html.twig',
                ],
                'statistics_chart' => [
                    'template' => 'dashboard/index/content/statistics_chart.html.twig',
                ],
            ],

            'sylius_admin.dashboard.index.content.statistics.statistics_grid' => [
                'talks' => [
                    'template' => 'dashboard/index/content/statistics_grid/talks.html.twig',
                ],
                'speakers' => [
                    'template' => 'dashboard/index/content/statistics_grid/speakers.html.twig',
                ],
                'conferences' => [
                    'template' => 'dashboard/index/content/statistics_grid/conferences.html.twig',
                ],
            ],

            'sylius_admin.dashboard.index.content.latest_updates' => [
                'new_talks' => [
                    'component' => NewTalksComponent::class,
                ],
                'new_speakers' => [
                    'component' => NewSpeakersComponent::class,
                ],
            ],
        ],
    ]);
};
