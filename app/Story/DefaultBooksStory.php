<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\BookFactory;
use Zenstruck\Foundry\Story;

final class DefaultBooksStory extends Story
{
    public function build(): void
    {
        BookFactory::new()
            ->withTitle('1984')
            ->withAuthorName('George Orwell')
        ;

        BookFactory::createMany(20);
    }
}
