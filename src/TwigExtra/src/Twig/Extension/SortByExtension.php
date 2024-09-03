<?php

declare(strict_types=1);

namespace Sylius\TwigExtra\Twig\Extension;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SortByExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('sort_by', [$this, 'sortBy']),
        ];
    }

    /**
     * @param iterable<array<array-key, mixed>|object> $iterable
     *
     * @return array<array<array-key, mixed>|object>
     *
     * @throws NoSuchPropertyException
     */
    public function sortBy(iterable $iterable, string $field, string $order = 'ASC'): array
    {
        $array = $this->transformIterableToArray($iterable);

        usort(
            $array,
            function (array|object $firstElement, array|object $secondElement) use ($field, $order): int {
                $accessor = PropertyAccess::createPropertyAccessor();

                $firstProperty = (string) $accessor->getValue($firstElement, $field);
                $secondProperty = (string) $accessor->getValue($secondElement, $field);

                $result = strnatcasecmp($firstProperty, $secondProperty);
                if ('DESC' === $order) {
                    $result *= -1;
                }

                return $result;
            },
        );

        return $array;
    }

    /**
     * @param iterable<array<array-key, mixed>|object> $iterable
     *
     * @return array<array<array-key, mixed>|object>
     */
    private function transformIterableToArray(iterable $iterable): array
    {
        if (is_array($iterable)) {
            return $iterable;
        }

        return iterator_to_array($iterable);
    }
}
