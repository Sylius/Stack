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

namespace Sylius\TwigExtra\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestHtmlAttributeExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $environment,
        private readonly bool $isDebugEnabled,
    ) {
    }

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_test_html_attribute',
                function (string $name, ?string $value = null): string {
                    if (str_starts_with($this->environment, 'test') || $this->isDebugEnabled) {
                        $escapedValue = htmlspecialchars((string) $value, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

                        return sprintf('data-test-%s="%s"', $name, $escapedValue);
                    }

                    return '';
                },
                ['is_safe' => ['html']],
            ),
        ];
    }
}
