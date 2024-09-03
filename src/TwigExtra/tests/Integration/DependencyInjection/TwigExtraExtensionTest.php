<?php

declare(strict_types=1);

namespace Tests\Sylius\TwigExtra\Integration\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\TwigExtra\Symfony\DependencyInjection\TwigExtraExtension;
use Sylius\TwigExtra\Twig\Ux\ComponentTemplateFinder;

final class TwigExtraExtensionTest extends AbstractExtensionTestCase
{
    public function testItRegistersTwigUxComponentTemplateFinder(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('sylius_twig_extra.twig.ux.component_template_finder', ComponentTemplateFinder::class);
    }

    public function testItRegistersTwigUxAnonymousComponentTemplatePrefixesParameter(): void
    {
        $this->load([
            'twig_ux' => [
                'anonymous_component_template_prefixes' => [
                    'sylius_admin_ui:component' => '@SyliusAdminUi/components',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('sylius_twig_extra.twig_ux.anonymous_component_template_prefixes', [
            'sylius_admin_ui:component' => '@SyliusAdminUi/components',
        ]);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new TwigExtraExtension(),
        ];
    }
}
