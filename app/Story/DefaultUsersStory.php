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

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DefaultUsersStory extends Story
{
    public function build(): void
    {
        UserFactory::new()
            ->admin()
            ->withEmail('admin@example.com')
            ->withPassword('admin')
            ->create()
        ;

        UserFactory::new()
            ->admin()
            ->withEmail('front@example.com')
            ->withPassword('front')
            ->create()
        ;
    }
}
