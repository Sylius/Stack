<?php

namespace App\Twig\Component;

use App\Grid\SpeakerGrid;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Sylius\UXLiveComponentExtra\Twig\Component\Grid\ComponentWithDataTableTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(
    name: 'admin_speaker_grid',
    template: '@SyliusBootstrapAdminUi/shared/components/grid/data_table.html.twig',
    route: 'sylius_admin_ui_live_component'
)]
class SpeakerGridComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentWithDataTableTrait;

    protected function getGrid(): string
    {
        return SpeakerGrid::class;
    }
}
