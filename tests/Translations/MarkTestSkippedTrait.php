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

namespace MainTests\Sylius\Translations;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @mixin KernelTestCase
 */
trait MarkTestSkippedTrait
{
    private function markTestSkippedIfNecessary(string $locale): void
    {
        if ($this->getContainer()->getParameter('kernel.default_locale') !== $locale) {
            $this->markTestSkipped();
        }
    }
}
