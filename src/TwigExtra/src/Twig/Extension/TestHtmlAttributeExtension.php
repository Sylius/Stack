<?php

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
                        return sprintf('data-test-%s="%s"', $name, (string) $value);
                    }

                    return '';
                },
                ['is_safe' => ['html']],
            ),
        ];
    }
}
