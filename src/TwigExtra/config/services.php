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

use Sylius\TwigExtra\Twig\Extension\SortByExtension;
use Sylius\TwigExtra\Twig\Extension\TestFormAttributeExtension;
use Sylius\TwigExtra\Twig\Extension\TestHtmlAttributeExtension;
use Sylius\TwigExtra\Twig\Ux\ComponentTemplateFinder;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_twig_extra.twig.extension.sort_by', SortByExtension::class)
        ->tag(name: 'twig.extension')
    ;

    $services->set('sylius_twig_extra.twig.extension.test_form_attribute', TestFormAttributeExtension::class)
        ->args([
            param('kernel.environment'),
            param('kernel.debug'),
        ])
        ->tag(name: 'twig.extension')
    ;

    $services->set('sylius_twig_extra.twig.extension.test_html_attribute', TestHtmlAttributeExtension::class)
        ->args([
            param('kernel.environment'),
            param('kernel.debug'),
        ])
        ->tag(name: 'twig.extension')
    ;

    $services->set('sylius_twig_extra.twig.ux.component_template_finder', ComponentTemplateFinder::class)
        ->decorate('ux.twig_component.component_template_finder')
        ->args([
            service('.inner'),
            service('twig.loader.native_filesystem'),
            param('sylius_twig_extra.twig_ux.anonymous_component_template_prefixes'),
        ])
    ;
};
