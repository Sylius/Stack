<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class DefaultUsersStory extends Story
{
    public function build(): void
    {
        UserFactory::new()
            ->withEmail('admin@example.com')
            ->withPassword('admin')
            ->withRoles(['ROLE_ADMIN'])
            ->create()
        ;
    }
}
