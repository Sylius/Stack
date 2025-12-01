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

namespace Tests\Sylius\TwigHooks\Integration\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\TwigHooks\DependencyInjection\Configuration;
use Sylius\TwigHooks\Hookable\DisabledHookable;
use Sylius\TwigHooks\Hookable\HookableComponent;
use Sylius\TwigHooks\Hookable\HookableTemplate;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testItReturnsDefaultConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            [
                'hooks' => [],
                'enable_autoprefixing' => false,
                'supported_hookable_types' => [
                    'template' => HookableTemplate::class,
                    'component' => HookableComponent::class,
                    'disabled' => DisabledHookable::class,
                ],
                'hook_name_section_separator' => false,
            ],
        );
    }

    public function testItSetsDefaultValuesForHookable(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'hooks' => [
                        'some_hook' => [
                            'some_hookable' => [
                                'template' => 'some_template.html.twig',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'hooks' => [
                    'some_hook' => [
                        'some_hookable' => [
                            'type' => 'template',
                            'template' => 'some_template.html.twig',
                            'context' => [],
                            'configuration' => [],
                            'priority' => null,
                            'enabled' => true,
                            'component' => null,
                            'props' => [],
                        ],
                    ],
                ],
            ],
            'hooks.*',
        );
    }

    public function testItAllowsToDefineSupportedHookableTypes(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'supported_hookable_types' => [
                        'template' => HookableTemplate::class,
                        'component' => HookableComponent::class,
                    ],
                ],
            ],
            [
                'supported_hookable_types' => [
                    'template' => HookableTemplate::class,
                    'component' => HookableComponent::class,
                ],
            ],
            'supported_hookable_types',
        );
    }

    public function testItAllowsToUseComponentShortcut(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'hooks' => [
                        'some_hook' => [
                            'some_hookable' => [
                                'component' => 'MyAwesomeComponent',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'hooks' => [
                    'some_hook' => [
                        'some_hookable' => [
                            'type' => 'component',
                            'context' => [],
                            'configuration' => [],
                            'priority' => null,
                            'enabled' => true,
                            'component' => 'MyAwesomeComponent',
                            'template' => null,
                            'props' => [],
                        ],
                    ],
                ],
            ],
            'hooks.*',
        );
    }

    public function testItAllowsToUseTemplateShortcut(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                [
                    'hooks' => [
                        'some_hook' => [
                            'some_hookable' => [
                                'template' => 'some_target.html.twig',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'hooks' => [
                    'some_hook' => [
                        'some_hookable' => [
                            'type' => 'template',
                            'context' => [],
                            'configuration' => [],
                            'priority' => null,
                            'enabled' => true,
                            'component' => null,
                            'template' => 'some_target.html.twig',
                            'props' => [],
                        ],
                    ],
                ],
            ],
            'hooks.*',
        );
    }

    public function testItThrowsExceptionWhenBothTemplateAndComponentShortcutsAreDefined(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'hooks' => [
                        'some_hook' => [
                            'some_hookable' => [
                                'component' => 'MyAwesomeComponent',
                                'template' => 'some_target.html.twig',
                                'type' => 'disabled',
                            ],
                        ],
                    ],
                ],
            ],
            'You cannot define both "component" and "template" at the same time.',
        );
    }

    public function testItThrowsExceptionWhenPropsAreDefinedForNonComponentHookable(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [
                    'hooks' => [
                        'some_hook' => [
                            'some_hookable' => [
                                'template' => 'some_target.html.twig',
                                'props' => ['key' => 'value'],
                                'type' => 'disabled',
                            ],
                        ],
                    ],
                ],
            ],
            '"Props" cannot be defined for non-component hookables.',
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
