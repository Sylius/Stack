<?php

declare(strict_types=1);

namespace Tests\Sylius\TwigExtra\Unit\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sylius\TwigExtra\Twig\Extension\SortByExtension;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Twig\Extension\ExtensionInterface;

final class SortByExtensionTest extends TestCase
{
    public function testItIsATwigExtension(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, new SortByExtension());
    }

    public function testItSortsAnArrayOfObjectsInAscendingOrderByDefault(): void
    {
        $firstData = (object) ['number' => 3];
        $secondData = (object) ['number' => 5];
        $thirdData = (object) ['number' => 1];

        $arrayBeforeSorting = [
            $firstData,
            $secondData,
            $thirdData,
        ];

        $this->assertEquals([
            $thirdData,
            $firstData,
            $secondData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, 'number'));
    }

    public function testItSortsAnIterableOfObjectsInAscendingOrderByDefault(): void
    {
        $firstData = (object) ['number' => 3];
        $secondData = (object) ['number' => 5];
        $thirdData = (object) ['number' => 1];

        $iterable = new ObjectCollection($firstData, $secondData, $thirdData);

        $this->assertEquals([
            $thirdData,
            $firstData,
            $secondData,
        ], (new SortByExtension())->sortBy($iterable, 'number'));
    }

    public function testItSortsAnArrayOfArraysInAscendingOrderByDefault(): void
    {
        $firstData = ['number' => 3];
        $secondData = ['number' => 5];
        $thirdData = ['number' => 1];

        $arrayBeforeSorting = [
            $firstData,
            $secondData,
            $thirdData,
        ];

        $this->assertEquals([
            $secondData,
            $firstData,
            $thirdData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, '[number]', 'DESC'));
    }

    public function testItSortsAnArrayOfObjectsByVariousProperties(): void
    {
        $firstData = (object) ['number' => 3, 'string' => 'true', 'bizarrelyNamedProperty' => 'banana'];
        $secondData = (object) ['number' => 5, 'string' => '123', 'bizarrelyNamedProperty' => 123];
        $thirdData = (object) ['number' => 1, 'string' => 'Alohomora', 'bizarrelyNamedProperty' => null];

        $arrayBeforeSorting = [
            $firstData,
            $secondData,
            $thirdData,
        ];

        $this->assertEquals([
            $thirdData,
            $firstData,
            $secondData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, 'number'));

        $this->assertEquals([
            $secondData,
            $thirdData,
            $firstData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, 'string'));

        $this->assertEquals([
            $thirdData,
            $secondData,
            $firstData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, 'bizarrelyNamedProperty'));
    }

    public function testItSortsAnArrayOfObjectsInDescendingOrderByAProperty(): void
    {
        $firstData = (object) ['number' => 3];
        $secondData = (object) ['number' => 5];
        $thirdData = (object) ['number' => 1];

        $arrayBeforeSorting = [
            $firstData,
            $secondData,
            $thirdData,
        ];

        $this->assertEquals([
            $secondData,
            $firstData,
            $thirdData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, 'number', 'DESC'));
    }

    public function testItSortsAnArrayOfObjectsByANestedProperty(): void
    {
        $firstData = (object) ['data' => (object) ['number' => 3]];
        $secondData = (object) ['data' =>  (object) ['number' => 5]];
        $thirdData = (object) ['data' =>  (object) ['number' => 1]];

        $arrayBeforeSorting = [
            $firstData,
            $secondData,
            $thirdData,
        ];

        $this->assertEquals([
            $thirdData,
            $firstData,
            $secondData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, 'data.number'));
    }

    public function testItSortsAnArrayOfArraysByANestedProperty(): void
    {
        $firstData = ['data' => ['number' => 3]];
        $secondData = ['data' =>  ['number' => 5]];
        $thirdData = ['data' =>  ['number' => 1]];

        $arrayBeforeSorting = [
            $firstData,
            $secondData,
            $thirdData,
        ];

        $this->assertEquals([
            $thirdData,
            $firstData,
            $secondData,
        ], (new SortByExtension())->sortBy($arrayBeforeSorting, '[data][number]'));
    }

    public function testItThrowsAnExceptionIfThePropertyIsNotFoundOnObjects(): void
    {
        $arrayBeforeSorting = [
            (object) [],
            (object) [],
            (object) [],
        ];

        $this->expectException(NoSuchPropertyException::class);

        (new SortByExtension())->sortBy($arrayBeforeSorting, 'nonExistingProperty');
    }

    public function testItReturnsInputArrayIfThereIsOnlyOneObjectInside(): void
    {
        $data = [(object) []];

        $this->assertEquals($data, (new SortByExtension())->sortBy($data, 'property'));
    }

    public function testItDoesNothingIfArrayIsEmpty(): void
    {
        $this->assertEquals([], (new SortByExtension())->sortBy([], 'property'));
    }

    public function testItDoesNothingIfAnIterableIsEmpty(): void
    {
        $this->assertEquals([], (new SortByExtension())->sortBy(new ObjectCollection(), 'property'));
    }
}

/**
 * @implements \IteratorAggregate<object>
 */
final class ObjectCollection implements \IteratorAggregate
{
    /** @var object[] */
    private array $data;

    public function __construct(object ...$data)
    {
        $this->data = $data;
    }

    public function getIterator(): \Traversable
    {
        yield from array_values($this->data);
    }
}
