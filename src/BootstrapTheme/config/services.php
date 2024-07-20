<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\BootstrapTheme\Symfony\Form\Type\Grid\Filter\UxAutocompleteFilterType;
use Sylius\Component\Grid\Filter\EntityFilter;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
    ;

    $services->set('sylius_bootstrap_theme.grid_filter.ux_autocomplete', EntityFilter::class)
        ->tag(name: 'sylius.grid_filter', attributes: ['type' => 'ux_autocomplete', 'form_type' => UxAutocompleteFilterType::class])
    ;

    $services->set('sylius_bootstrap_theme.form.type.grid_filter.ux_autocomplete', UxAutocompleteFilterType::class)
        ->tag(name: 'form.type')
        ->tag(name: 'ux.entity_autocomplete_field')
    ;
};
