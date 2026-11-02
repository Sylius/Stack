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

namespace TestApplication\Sylius\TwigHooks\Component;

use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('app:context_aware_live', template: '_component/context_aware_live.html.twig')]
final class ContextAwareLiveComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public string $productName = '';

    /**
     * @return array<string>
     */
    protected function getDehydratedHookableContextKeys(): array
    {
        return ['flag', 'not_in_context'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function provideHookableContext(): array
    {
        return ['product' => (object) ['name' => $this->productName]];
    }
}
