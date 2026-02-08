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

namespace App\Enum;

enum BookCategory: string
{
    case FICTION = 'fiction';
    case NON_FICTION = 'non-fiction';
    case MYSTERY = 'mystery';
    case THRILLER = 'thriller';
    case SCIENCE_FICTION = 'science-fiction';
    case FANTASY = 'fantasy';
    case ROMANCE = 'romance';
    case HORROR = 'horror';
    case BIOGRAPHY = 'biography';
    case HISTORY = 'history';
    case SCIENCE = 'science';
    case TECHNOLOGY = 'technology';
    case BUSINESS = 'business';
    case COOKING = 'cooking';
    case TRAVEL = 'travel';
    case POETRY = 'poetry';
    case DRAMA = 'drama';
    case CHILDREN = 'children';
    case YOUNG_ADULT = 'young adult';
    case RELIGION = 'religion';
    case PHILOSOPHY = 'philosophy';
    case ART = 'art';
    case MANGA = 'manga';
    case COMICS = 'comics';
}
