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

namespace App\Twig\Component;

use App\Grid\SpeakerGrid;
use Sylius\TwigComponentGrid\Twig\Component\ComponentWithDataTableTrait;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'admin_speaker_grid',
    template: '@SyliusBootstrapAdminUi/shared/components/grid/data_table.html.twig',
    route: 'sylius_admin_ui_live_component',
)]
class SpeakerDataTableComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentWithDataTableTrait;

    protected function getGrid(): string
    {
        return SpeakerGrid::class;
    }
}
