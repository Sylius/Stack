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

namespace Tests\Sylius\TwigHooks\Unit\Bag;

use PHPUnit\Framework\TestCase;
use Sylius\TwigHooks\Bag\DataBag;

final class DataBagTest extends TestCase
{
    public function testItCanBeCreatedFromArray(): void
    {
        $dataBag = new DataBag(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $dataBag->all());
    }

    public function testItCreatesEmptyBagByDefault(): void
    {
        $dataBag = new DataBag();

        $this->assertEmpty($dataBag->all());
    }

    public function testItReturnsWhetherGivenOffsetExists(): void
    {
        $dataBag = new DataBag(['foo' => 'bar', 'null_value' => null]);

        $this->assertTrue(isset($dataBag['foo']));
        $this->assertTrue(isset($dataBag['null_value']));
        $this->assertFalse(isset($dataBag['bar']));
    }

    public function testItReturnsGivenOffset(): void
    {
        $dataBag = new DataBag(['foo' => 'bar']);

        $this->assertSame('bar', $dataBag['foo']);
        $this->assertNull($dataBag['bar']);
    }

    public function testItSetsGivenOffset(): void
    {
        $dataBag = new DataBag();

        $dataBag['foo'] = 'bar';

        $this->assertSame('bar', $dataBag['foo']);
    }

    public function testItThrowsExceptionIfOffsetIsNotString(): void
    {
        $dataBag = new DataBag();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The offset must be a string.');

        $dataBag[1] = 'bar';
    }

    public function testItThrowsExceptionIfOffsetIsEmptyString(): void
    {
        $dataBag = new DataBag();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The offset must not be an empty string.');

        $dataBag[''] = 'bar';
    }

    public function testItUnsetsGivenOffset(): void
    {
        $dataBag = new DataBag(['foo' => 'bar']);

        unset($dataBag['foo']);

        $this->assertNull($dataBag['foo']);
    }

    public function testItReturnsGivenProperty(): void
    {
        $dataBag = new DataBag(['foo' => 'bar']);

        $this->assertSame('bar', $dataBag->foo);
        $this->assertNull($dataBag->bar);
    }

    public function testItReturnsWhetherGivenPropertyExists(): void
    {
        $dataBag = new DataBag(['foo' => 'bar', 'null_value' => null]);

        $this->assertTrue($dataBag->has('foo'));
        $this->assertTrue($dataBag->has('null_value'));
        $this->assertFalse($dataBag->has('bar'));
    }

    public function testItReturnsWhetherGivenPropertyIsSet(): void
    {
        $dataBag = new DataBag(['foo' => 'bar', 'null_value' => null]);

        $this->assertTrue(isset($dataBag->foo));
        $this->assertTrue(isset($dataBag->null_value));
        $this->assertFalse(isset($dataBag->baz));
    }
}
