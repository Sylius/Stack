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

namespace Tests\Sylius\BootstrapAdminUi\Functional;

use Sylius\TwigHooks\Hookable\AbstractHookable;
use Sylius\TwigHooks\Registry\HookablesRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class SymfonyUxTemplatesTest extends KernelTestCase
{
    public function testItLoadsThePrebuiltSymfonyUxAssetsWithoutImportmapEntrypoint(): void
    {
        $context = ['hookable_metadata' => ['configuration' => []]];

        $this->assertStringContainsString('symfony_ux', $this->render('symfony_ux_styles.html.twig', $context));
        $this->assertStringContainsString('symfony_ux', $this->render('symfony_ux_scripts.html.twig', $context));
    }

    public function testItDoesNotLoadThePrebuiltStimulusApplicationWithAnImportmapEntrypoint(): void
    {
        $context = ['hookable_metadata' => ['configuration' => ['entrypoint' => 'app']]];

        $this->assertSame('', trim($this->render('symfony_ux_scripts.html.twig', $context)));
    }

    public function testItRendersThePackageStylesheetsBeforeTheImportmapEntrypoint(): void
    {
        self::bootKernel(['environment' => 'test_importmap']);

        /** @var HookablesRegistry $registry */
        $registry = self::getContainer()->get('sylius_twig_hooks.registry.hookables');

        $names = array_map(
            static fn (AbstractHookable $hookable): string => $hookable->name,
            $registry->getEnabledFor('sylius_admin.base#stylesheets'),
        );

        $this->assertLessThan(array_search('symfony_ux', $names, true), array_search('styles', $names, true));
    }

    /**
     * @param array<string, mixed> $context
     */
    private function render(string $template, array $context): string
    {
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');

        return $twig->render('@SyliusBootstrapAdminUi/shared/layout/' . $template, $context);
    }
}
