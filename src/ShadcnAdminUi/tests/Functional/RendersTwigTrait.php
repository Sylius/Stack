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

namespace Tests\Sylius\ShadcnAdminUi\Functional;

use Symfony\Component\DomCrawler\Crawler;
use Twig\Environment;

trait RendersTwigTrait
{
    /**
     * @param array<string, mixed> $context
     */
    private function render(string $template, array $context = []): string
    {
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');

        return $twig->createTemplate($template)->render($context);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function crawl(string $template, array $context = []): Crawler
    {
        return new Crawler($this->render($template, $context));
    }
}
