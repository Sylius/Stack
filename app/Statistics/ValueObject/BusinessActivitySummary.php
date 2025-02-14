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

namespace App\Statistics\ValueObject;

final class BusinessActivitySummary
{
    public function __construct(
        public int $totalTalks,
        public int $totalSpeakers,
        public int $totalConferences,
    ) {
    }
}
