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

namespace Tests\Sylius\TwigExtra\Unit\Twig\Ux;

use PHPUnit\Framework\TestCase;
use Sylius\TwigExtra\Twig\Ux\ComponentTemplateFinder;
use Symfony\UX\TwigComponent\ComponentTemplateFinder as DecoratedComponentTemplateFinder;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;

final class ComponentTemplateFinderTest extends TestCase
{
    public function testItCallsDecoratedFinderIfNoPrefixMatches(): void
    {
        $componentTemplateFinder = $this->createTemplateFinder(
            ['components/sylius_admin_ui/component.html.twig' => ''],
            [],
        );

        $this->assertEquals(
            'components/sylius_admin_ui/component.html.twig',
            $componentTemplateFinder->findAnonymousComponentTemplate('sylius_admin_ui:component'),
        );
    }

    public function testItFindsAnonymousComponentTemplate(): void
    {
        $componentTemplateFinder = $this->createTemplateFinder(
            ['@SyliusBootstrapTheme/components/some_sub_component.html.twig' => ''],
            ['sylius_admin_ui:component' => '@SyliusBootstrapTheme/components'],
        );

        $this->assertEquals(
            '@SyliusBootstrapTheme/components/some_sub_component.html.twig',
            $componentTemplateFinder->findAnonymousComponentTemplate('sylius_admin_ui:component:some_sub_component'),
        );
    }

    private function createTemplateFinder(array $templates, array $anonymousComponentTemplatePrefixes): ComponentTemplateFinder
    {
        return new ComponentTemplateFinder(
            new DecoratedComponentTemplateFinder(
                $this->createLoader($templates),
                'components',
            ),
            $this->createLoader($templates),
            $anonymousComponentTemplatePrefixes,
        );
    }

    private function createLoader(array $templates): LoaderInterface
    {
        return new ArrayLoader($templates);
    }
}
