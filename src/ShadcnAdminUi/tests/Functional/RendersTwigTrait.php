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
    private function render(string $template): string
    {
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');

        return $twig->createTemplate($template)->render();
    }

    private function crawl(string $template): Crawler
    {
        return new Crawler($this->render($template));
    }
}
