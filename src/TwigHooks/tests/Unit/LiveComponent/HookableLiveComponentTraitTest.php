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

namespace Tests\Sylius\TwigHooks\Unit\LiveComponent;

use PHPUnit\Framework\TestCase;
use Sylius\TwigHooks\Bag\DataBag;
use Sylius\TwigHooks\Bag\ScalarDataBag;
use Sylius\TwigHooks\Hook\Metadata\HookMetadata;
use Sylius\TwigHooks\Hookable\Metadata\HookableMetadata;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;

final class HookableLiveComponentTraitTest extends TestCase
{
    public function testItDehydratesWithoutTheContextByDefault(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;
        };

        $this->assertSame(
            [
                'renderedBy' => 'some_hook',
                'configuration' => '{"some":"configuration"}',
                'prefixes' => ['some_hook'],
            ],
            $component->dehydrateHookableMetadata($this->createMetadata(['flag' => true, 'object' => new \stdClass()])),
        );
    }

    public function testItDehydratesTheListedContextValues(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;

            protected function getDehydratedHookableContextKeys(): array
            {
                return ['flag', 'ids', 'nullable', 'not_in_context'];
            }
        };

        $data = $component->dehydrateHookableMetadata($this->createMetadata([
            'flag' => true,
            'ids' => [1, 2, ['nested' => 'value']],
            'nullable' => null,
            'object' => new \stdClass(),
        ]));

        $this->assertSame(['flag' => true, 'ids' => [1, 2, ['nested' => 'value']], 'nullable' => null], $data['context']);
    }

    public function testItThrowsAnExceptionWhenAListedContextValueCannotBeDehydrated(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;

            protected function getDehydratedHookableContextKeys(): array
            {
                return ['objects'];
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "objects" hookable context value');

        $component->dehydrateHookableMetadata($this->createMetadata(['objects' => [new \stdClass()]]));
    }

    public function testItHydratesTheDehydratedContext(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;
        };

        $metadata = $component->hydrateHookableMetadata([
            'renderedBy' => 'some_hook',
            'configuration' => '{"some":"configuration"}',
            'prefixes' => ['some_hook'],
            'context' => ['flag' => true],
        ]);

        $this->assertSame('some_hook', $metadata->renderedBy->name);
        $this->assertSame(['flag' => true], $metadata->renderedBy->context->all());
        $this->assertSame(['flag' => true], $metadata->context->all());
        $this->assertSame(['some' => 'configuration'], $metadata->configuration->all());
        $this->assertSame(['some_hook'], $metadata->prefixes);
    }

    public function testItHydratesDataDehydratedWithoutTheContext(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;
        };

        $metadata = $component->hydrateHookableMetadata([
            'renderedBy' => 'some_hook',
            'configuration' => '[]',
            'prefixes' => [],
        ]);

        $this->assertSame([], $metadata->context->all());
        $this->assertNull($component->hydrateHookableMetadata(null));
    }

    public function testItExposesTheHookableMetadataAsIsWhenNoContextIsProvided(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;
        };
        $component->hookableMetadata = $this->createMetadata(['flag' => true]);

        $this->assertSame($component->hookableMetadata, $component->getHookableMetadataForTemplate());
    }

    public function testItExposesTheHookableMetadataWithTheProvidedContext(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;

            public ?object $product = null;

            protected function provideHookableContext(): array
            {
                return ['product' => $this->product];
            }
        };
        $component->product = new \stdClass();
        $component->hookableMetadata = $this->createMetadata(['flag' => true, 'product' => 'overridden']);

        $metadata = $component->getHookableMetadataForTemplate();

        $this->assertSame(['flag' => true, 'product' => $component->product], $metadata->context->all());
        $this->assertSame($component->hookableMetadata->renderedBy, $metadata->renderedBy);
        $this->assertSame($component->hookableMetadata->configuration, $metadata->configuration);
        $this->assertSame(['some_hook'], $metadata->prefixes);
    }

    public function testItExposesNoHookableMetadataWhenTheComponentIsNotHookable(): void
    {
        $component = new class() {
            use HookableLiveComponentTrait;

            protected function provideHookableContext(): array
            {
                return ['product' => new \stdClass()];
            }
        };

        $this->assertNull($component->getHookableMetadataForTemplate());
    }

    /**
     * @param array<string, mixed> $context
     */
    private function createMetadata(array $context): HookableMetadata
    {
        return new HookableMetadata(
            new HookMetadata('some_hook', new DataBag($context)),
            new DataBag($context),
            new ScalarDataBag(['some' => 'configuration']),
            ['some_hook'],
        );
    }
}
