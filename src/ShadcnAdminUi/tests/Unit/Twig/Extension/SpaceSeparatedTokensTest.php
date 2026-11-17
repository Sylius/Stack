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

namespace Tests\Sylius\ShadcnAdminUi\Unit\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sylius\ShadcnAdminUi\Twig\Extension\SpaceSeparatedTokens;

final class SpaceSeparatedTokensTest extends TestCase
{
    public function testItJoinsItsTokensWithSpaces(): void
    {
        $tokens = new SpaceSeparatedTokens(['a#b', 'c#d']);

        self::assertSame('a#b c#d', $tokens->getValue());
        self::assertSame('a#b c#d', (string) $tokens);
    }

    public function testItMergesIntoAPreviousList(): void
    {
        $merged = (new SpaceSeparatedTokens('b'))->mergeInto(new SpaceSeparatedTokens('a'));

        self::assertSame('a b', $merged->getValue());
    }

    public function testItAppendsFromANewList(): void
    {
        $appended = (new SpaceSeparatedTokens('a'))->appendFrom(['b', 'c']);

        self::assertSame('a b c', $appended->getValue());
    }

    public function testItIgnoresNullAndFalseTokens(): void
    {
        self::assertSame('a b', (new SpaceSeparatedTokens(['a', null, false, 'b']))->getValue());
    }

    public function testAnEmptyListHasNoValue(): void
    {
        $tokens = new SpaceSeparatedTokens([null]);

        self::assertNull($tokens->getValue());
        self::assertSame('', (string) $tokens);
    }
}
