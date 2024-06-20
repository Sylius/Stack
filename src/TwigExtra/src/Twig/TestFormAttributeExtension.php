<?php

declare(strict_types=1);

namespace Sylius\TwigExtra\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestFormAttributeExtension extends AbstractExtension
{
    public function __construct(private string $environment)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_test_form_attribute',
                function (string $name, ?string $value = null): array {
                    if (str_starts_with($this->environment, 'test')) {
                        return ['attr' => ['data-test-' . $name => (string) $value]];
                    }

                    return [];
                },
                ['is_safe' => ['html']],
            ),
        ];
    }
}
