<?php

declare(strict_types=1);

namespace Sylius\TwigExtra\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestFormAttributeExtension extends AbstractExtension
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
                'sylius_test_form_attribute',
                function (string $name, ?string $value = null): array {
                    if (str_starts_with($this->environment, 'test') || $this->isDebugEnabled) {
                        return ['attr' => ['data-test-' . $name => (string) $value]];
                    }

                    return [];
                },
                ['is_safe' => ['html']],
            ),
        ];
    }
}
