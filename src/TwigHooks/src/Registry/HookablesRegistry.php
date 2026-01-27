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

namespace Sylius\TwigHooks\Registry;

use Laminas\Stdlib\SplPriorityQueue;
use Sylius\TwigHooks\Hookable\AbstractHookable;
use Sylius\TwigHooks\Hookable\DisabledHookable;
use Sylius\TwigHooks\Hookable\Merger\HookableMergerInterface;

/** @internal */
class HookablesRegistry
{
    /** @var array<string, array<AbstractHookable>> */
    private array $hookables = [];

    /**
     * @param iterable<AbstractHookable> $hookables
     */
    public function __construct(
        iterable $hookables,
        private readonly HookableMergerInterface $hookableMerger,
    ) {
        /** @var AbstractHookable $hookable */
        foreach ($hookables as $hookable) {
            if (!$hookable instanceof AbstractHookable) {
                throw new \InvalidArgumentException(
                    sprintf('All elements must be an instance of "%s".', AbstractHookable::class),
                );
            }

            $this->hookables[$hookable->hookName][$hookable->name] = $hookable;
        }
    }

    /**
     * @return array<string>
     */
    public function getHookNames(): array
    {
        return array_keys($this->hookables);
    }

    /**
     * @param string|array<string> $hooksNames
     *
     * @return array<AbstractHookable>
     */
    public function getEnabledFor(string|array $hooksNames): array
    {
        $hooksNames = is_string($hooksNames) ? [$hooksNames] : $hooksNames;
        $hookables = array_values(
            array_filter(
                $this->mergeHookables($hooksNames),
                static fn (AbstractHookable $hookable): bool => !$hookable instanceof DisabledHookable,
            ),
        );

        $priorityQueue = new SplPriorityQueue();
        foreach ($hookables as $hookable) {
            $priorityQueue->insert($hookable, $hookable->priority());
        }

        return $priorityQueue->toArray();
    }

    /**
     * @param string|array<string> $hooksNames
     *
     * @return array<AbstractHookable>
     */
    public function getAllFor(string|array $hooksNames): array
    {
        $hooksNames = is_string($hooksNames) ? [$hooksNames] : $hooksNames;
        $hookables = $this->mergeHookables($hooksNames);

        $priorityQueue = new SplPriorityQueue();
        foreach ($hookables as $hookable) {
            $priorityQueue->insert($hookable, $hookable->priority());
        }

        return $priorityQueue->toArray();
    }

    /**
     * @param array<string> $hooksNames
     *
     * @return array<AbstractHookable>
     */
    private function mergeHookables(array $hooksNames): array
    {
        /** @var array<AbstractHookable> $mergedHookables */
        $mergedHookables = [];

        foreach (array_reverse($hooksNames) as $hookName) {
            $hookables = $this->hookables[$hookName] ?? [];

            foreach ($hookables as $hookableName => $hookable) {
                if (array_key_exists($hookableName, $mergedHookables)) {
                    $hookable = $this->hookableMerger->merge($mergedHookables[$hookableName], $hookable);
                }

                $mergedHookables[$hookableName] = $hookable;
            }
        }

        return array_values($mergedHookables);
    }
}
