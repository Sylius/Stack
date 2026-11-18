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

namespace Sylius\TwigHooks\LiveComponent;

use Sylius\TwigHooks\Bag\DataBag;
use Sylius\TwigHooks\Bag\ScalarDataBag;
use Sylius\TwigHooks\Hook\Metadata\HookMetadata;
use Sylius\TwigHooks\Hookable\Metadata\HookableMetadata;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * The hookable context is not sent to the client, as it usually contains objects (grid views, forms, entities...),
 * so it is gone during live re-renders. To keep it available in the component template and in its nested hooks:
 *  - list the scalar context values to keep in the LiveProp with {@see getDehydratedHookableContextKeys()};
 *  - give back the other values (built from the component's own LiveProps) with {@see provideHookableContext()}.
 */
trait HookableLiveComponentTrait
{
    #[LiveProp(hydrateWith: 'hydrateHookableMetadata', dehydrateWith: 'dehydrateHookableMetadata')]
    #[ExposeInTemplate(name: 'hookable_metadata', getter: 'getHookableMetadataForTemplate')]
    public ?HookableMetadata $hookableMetadata = null;

    /**
     * @param array{renderedBy: string, configuration: string, prefixes?: array<string>, context?: array<string, mixed>}|null $data
     */
    public function hydrateHookableMetadata($data): ?HookableMetadata
    {
        if (null === $data) {
            return null;
        }

        $context = new DataBag($data['context'] ?? []);

        return new HookableMetadata(
            new HookMetadata($data['renderedBy'], $context),
            $context,
            new ScalarDataBag(json_decode($data['configuration'], true)),
            $data['prefixes'] ?? [],
        );
    }

    /**
     * @return array{renderedBy: string, configuration: string, prefixes?: array<string>, context?: array<string, mixed>}|null
     */
    public function dehydrateHookableMetadata(?HookableMetadata $metadata = null): ?array
    {
        if (null === $metadata) {
            return null;
        }

        $data = [
            'renderedBy' => $metadata->renderedBy->name,
            'configuration' => json_encode($metadata->configuration->all(), \JSON_THROW_ON_ERROR),
            'prefixes' => $metadata->prefixes,
        ];

        $context = $this->dehydrateHookableContext($metadata);
        if ([] !== $context) {
            $data['context'] = $context;
        }

        return $data;
    }

    /**
     * The hookable metadata exposed as "hookable_metadata", with the context provided by the component
     * on top of the hookable context. Called at render time, on the first render and on every re-render.
     */
    public function getHookableMetadataForTemplate(): ?HookableMetadata
    {
        if (null === $this->hookableMetadata) {
            return null;
        }

        $providedContext = $this->provideHookableContext();
        if ([] === $providedContext) {
            return $this->hookableMetadata;
        }

        return $this->hookableMetadata->withContext(
            new DataBag(array_merge($this->hookableMetadata->context->all(), $providedContext)),
        );
    }

    /**
     * Keys of the hookable context to keep across re-renders. Their values must be scalars, nulls
     * or arrays of them. They are sent to the client: never list secrets here.
     *
     * @return array<string>
     */
    protected function getDehydratedHookableContextKeys(): array
    {
        return [];
    }

    /**
     * Context values merged on top of the hookable context at render time, on the first render and on every
     * re-render. Use it to give back the objects that cannot be sent to the client, from the component's LiveProps.
     *
     * @return array<string, mixed>
     */
    protected function provideHookableContext(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function dehydrateHookableContext(HookableMetadata $metadata): array
    {
        $hookableContext = $metadata->context->all();
        $context = [];

        foreach ($this->getDehydratedHookableContextKeys() as $key) {
            if (!array_key_exists($key, $hookableContext)) {
                continue;
            }

            $value = $hookableContext[$key];
            if (!self::isDehydratableHookableContextValue($value)) {
                throw new \LogicException(sprintf(
                    'The "%s" hookable context value of the "%s" component cannot be dehydrated, only scalars, nulls and arrays of them can. Provide it with "provideHookableContext()" instead.',
                    $key,
                    static::class,
                ));
            }

            $context[$key] = $value;
        }

        return $context;
    }

    private static function isDehydratableHookableContextValue(mixed $value): bool
    {
        if (null === $value || is_scalar($value)) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!self::isDehydratableHookableContextValue($item)) {
                return false;
            }
        }

        return true;
    }
}
