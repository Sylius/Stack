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

use Sylius\TwigHooks\Hook\Normalizer\Name\CompositeNameNormalizer;
use Sylius\TwigHooks\Hook\Normalizer\Name\NameNormalizerInterface;
use Sylius\TwigHooks\Hook\Normalizer\Prefix\CompositePrefixNormalizer;
use Sylius\TwigHooks\Hook\Normalizer\Prefix\PrefixNormalizerInterface;
use Sylius\TwigHooks\Hook\Normalizer\Prefix\RemoveSectionPartNormalizer;
use Sylius\TwigHooks\Hookable\Checker\HookableConditionChecker;
use Sylius\TwigHooks\Hookable\Checker\HookableConditionCheckerInterface;
use Sylius\TwigHooks\Hookable\Metadata\HookableMetadataFactory;
use Sylius\TwigHooks\Provider\ComponentPropsProvider;
use Sylius\TwigHooks\Provider\DefaultConfigurationProvider;
use Sylius\TwigHooks\Provider\DefaultContextProvider;
use Sylius\TwigHooks\Provider\PropsProviderInterface;
use Sylius\TwigHooks\Provider\TemplateConfigurationProvider;
use Sylius\TwigHooks\Provider\TemplateConfigurationProviderInterface;
use Sylius\TwigHooks\Registry\HookablesRegistry;
use Sylius\TwigHooks\Twig\HooksExtension;
use Sylius\TwigHooks\Twig\Runtime\HooksRuntime;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

return static function (ContainerConfigurator $configurator): void {
    $configurator->import(__DIR__ . '/services/*.php');

    $services = $configurator->services();

    $services->set('sylius_twig_hooks.provider.default_context', DefaultContextProvider::class);

    $services->set('sylius_twig_hooks.provider.component_props', ComponentPropsProvider::class)
        ->args([
            inline_service(ExpressionLanguage::class),
        ])
    ;
    $services->alias(PropsProviderInterface::class, 'sylius_twig_hooks.provider.component_props');

    $services->set('sylius_twig_hooks.provider.default_configuration', DefaultConfigurationProvider::class);

    $services->set('sylius_twig_hooks.provider.template_configuration', TemplateConfigurationProvider::class)
        ->args([
            inline_service(ExpressionLanguage::class),
        ])
    ;
    $services->alias(TemplateConfigurationProviderInterface::class, 'sylius_twig_hooks.provider.template_configuration');

    $services->set('sylius_twig_hooks.checker.hookable_condition', HookableConditionChecker::class)
        ->args([
            inline_service(ExpressionLanguage::class),
            service('security.authorization_checker')->nullOnInvalid(),
            service('security.token_storage')->nullOnInvalid(),
        ])
    ;
    $services->alias(HookableConditionCheckerInterface::class, 'sylius_twig_hooks.checker.hookable_condition');

    $services->set('sylius_twig_hooks.registry.hookables', HookablesRegistry::class)
        ->args([
            tagged_iterator('sylius_twig_hooks.hookable'),
            service('sylius_twig_hooks.merger.hookable'),
        ])
    ;

    $services
        ->set('sylius_twig_hooks.hook.normalizer.name.composite', CompositeNameNormalizer::class)
        ->args([
            tagged_iterator('sylius_twig_hooks.hook.name_normalizer'),
        ])
        ->alias(NameNormalizerInterface::class, 'sylius_twig_hooks.hook.normalizer.name.composite')
    ;

    $services
        ->set('sylius_twig_hooks.hook.normalizer.prefix.composite', CompositePrefixNormalizer::class)
        ->args([
            tagged_iterator('sylius_twig_hooks.hook.prefix_normalizer'),
        ])
        ->alias(PrefixNormalizerInterface::class, 'sylius_twig_hooks.hook.normalizer.prefix.composite')
    ;

    $services
        ->set('sylius_twig_hooks.hook.normalizer.prefix.remove_section_part', RemoveSectionPartNormalizer::class)
        ->args([
            param('sylius_twig_hooks.hook_name_section_separator'),
        ])
        ->tag('sylius_twig_hooks.hook.prefix_normalizer')
        ->alias(
            sprintf('%s $removeSectionPartNormalizer', PrefixNormalizerInterface::class),
            'sylius_twig_hooks.hook.normalizer.prefix.remove_section_part',
        )
    ;

    $services->set('sylius_twig_hooks.factory.hookable_metadata', HookableMetadataFactory::class)
        ->args([
            service('sylius_twig_hooks.hook.normalizer.prefix.composite'),
        ])
    ;

    $services->set(HooksExtension::class)
        ->args([
            service('sylius_twig_hooks.renderer.hook'),
            service('sylius_twig_hooks.registry.hookables'),
            service('sylius_twig_hooks.renderer.hookable'),
            service('sylius_twig_hooks.profiler.profile')->nullOnInvalid(),
            service('debug.stopwatch')->nullOnInvalid(),
        ])
        ->tag('twig.extension')
    ;

    $services->set(HooksRuntime::class)
        ->args([
            service('sylius_twig_hooks.renderer.hook'),
            service('sylius_twig_hooks.hook.normalizer.name.composite'),
            service('sylius_twig_hooks.hook.normalizer.prefix.composite'),
            param('sylius_twig_hooks.enable_autoprefixing'),
        ])
        ->tag('twig.runtime')
    ;
};
