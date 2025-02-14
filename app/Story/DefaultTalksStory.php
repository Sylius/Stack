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

namespace App\Story;

use App\Factory\TalkFactory;
use Zenstruck\Foundry\Story;

final class DefaultTalksStory extends Story
{
    public function build(): void
    {
        TalkFactory::new()->withStartingDate(new \DateTimeImmutable('first day of january this year'))->many(3)->create();
        TalkFactory::new()->withStartingDate(new \DateTimeImmutable('today'))->many(2)->create();

        TalkFactory::createMany(100);
    }
}
