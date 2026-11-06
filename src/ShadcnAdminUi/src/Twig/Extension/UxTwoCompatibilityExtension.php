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

namespace Sylius\ShadcnAdminUi\Twig\Extension;

use Symfony\UX\TwigComponent\ComponentStack;
use Symfony\UX\TwigComponent\MountedComponent;
use TalesFromADev\Twig\Extra\Tailwind\TailwindRuntime;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Html\HtmlExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Brings what the shadcn kit expects from symfony/ux-twig-component 3.5+ to Symfony UX 2.
 *
 * Only registered under Symfony UX 2: remove this class when Sylius Stack drops it.
 */
final class UxTwoCompatibilityExtension extends AbstractExtension
{
    /** @var \WeakMap<MountedComponent, array<string, mixed>> */
    private \WeakMap $provided;

    public function __construct(
        private readonly ComponentStack $componentStack,
        private readonly TailwindRuntime $tailwind,
    ) {
        $this->provided = new \WeakMap();
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('tailwind_classes', [$this, 'classes']),
            new TwigFilter('html_attr_type', [$this, 'attributeType']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('provide', [$this, 'provide']),
            new TwigFunction('inject', [$this, 'inject']),
        ];
    }

    public function provide(string $key, mixed $value): void
    {
        $current = $this->componentStack->getCurrentComponent();
        if (null === $current) {
            throw new \LogicException(sprintf('The "provide()" Twig function cannot be called outside of a component template, "%s" key was being provided.', $key));
        }

        $this->provided[$current] = [$key => $value] + ($this->provided[$current] ?? []);
    }

    public function inject(string $key, mixed $default = null): mixed
    {
        $isCurrent = true;
        foreach ($this->componentStack as $mounted) {
            if ($isCurrent) {
                $isCurrent = false;

                continue;
            }

            $provided = $this->provided[$mounted] ?? [];
            if (\array_key_exists($key, $provided)) {
                return $provided[$key];
            }
        }

        return $default;
    }

    /**
     * ComponentAttributes::defaults() only concatenates classes under Symfony UX 2, so the conflicts with
     * the classes of the caller are resolved here: only the default classes that survive are returned.
     *
     * This assumes the caller classes apply to the element using this filter, which is the root element.
     */
    public function classes(string|iterable|null $classes = null): mixed
    {
        $caller = $this->componentStack->getCurrentComponent()?->getAttributes()->all()['class'] ?? null;
        if ($caller instanceof \Stringable) {
            $caller = (string) $caller;
        }

        if (!\is_string($caller) || '' === $caller) {
            return $this->tailwind->classes($classes);
        }

        $defaults = is_iterable($classes) ? implode(' ', array_filter([...$classes])) : (string) $classes;
        $merged = $this->split($this->tailwind->merge($defaults . ' ' . $caller));

        return implode(' ', array_diff($merged, $this->split($caller)));
    }

    /**
     * ComponentAttributes::defaults() casts values to strings to concatenate them under Symfony UX 2,
     * which SeparatedTokenList does not support: space-separated lists stay plain strings.
     */
    public function attributeType(mixed $value, string $type = 'sst'): mixed
    {
        if ('sst' !== $type) {
            return HtmlExtension::htmlAttrType($value, $type);
        }

        return is_iterable($value) ? implode(' ', [...$value]) : $value;
    }

    /**
     * @return list<string>
     */
    private function split(string $classes): array
    {
        return preg_split('/\s+/', $classes, -1, \PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
