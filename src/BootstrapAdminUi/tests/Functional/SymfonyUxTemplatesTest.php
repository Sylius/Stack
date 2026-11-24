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
