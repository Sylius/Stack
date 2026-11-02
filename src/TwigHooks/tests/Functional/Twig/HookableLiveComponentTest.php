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

namespace Tests\Sylius\TwigHooks\Functional\Twig;

use Sylius\TwigHooks\Bag\DataBag;
use Sylius\TwigHooks\Bag\ScalarDataBag;
use Sylius\TwigHooks\Hook\Metadata\HookMetadata;
use Sylius\TwigHooks\Hookable\Metadata\HookableMetadata;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Twig\Environment as Twig;

/**
 * @group kernel-required
 */
final class HookableLiveComponentTest extends KernelTestCase
{
    use InteractsWithLiveComponents;

    public function testItRendersHookableLiveComponentWithUsingTwigHooks(): void
    {
        $this->assertSame(
            <<<EXPECTED
            <!-- BEGIN HOOK | name: "hookable_live_component" -->
            <!-- BEGIN HOOKABLE | hook: "hookable_live_component", name: "dummy", component: "app:dummy_live", priority: 0 -->
            I'm a hookable!
            <!--  END HOOKABLE  | hook: "hookable_live_component", name: "dummy", component: "app:dummy_live", priority: 0 -->
            <!--  END HOOK  | name: "hookable_live_component" -->
            EXPECTED,
            $this->render('hookable_live_component/hookable_component_rendered_with_using_twig_hooks.html.twig'),
        );
    }

    public function testItRendersHookableLiveComponentWithoutUsingTwigHooks(): void
    {
        $this->assertStringContainsString(
            "I'm not a hookable :(",
            $this->render('hookable_live_component/hookable_component_rendered_without_using_twig_hooks.html.twig'),
        );
    }

    public function testItRendersHookableLiveComponentWithTheHookableContext(): void
    {
        $rendered = $this->render(
            'hookable_live_component/hookable_component_rendered_with_context.html.twig',
            ['product' => (object) ['name' => 'Mug']],
        );

        $this->assertStringContainsString('flag: yes', $rendered);
        $this->assertStringContainsString('product: Mug', $rendered);
        $this->assertStringContainsString('secret: s3cr3t', $rendered);
        $this->assertStringContainsString('nested product: Mug', $rendered);
    }

    public function testItKeepsTheHookableContextOnReRender(): void
    {
        $hookName = 'hookable_live_component_with_context';
        $context = new DataBag(['flag' => true, 'product' => (object) ['name' => 'Mug'], 'secret' => 's3cr3t']);

        $component = $this->createLiveComponent('app:context_aware_live', [
            'hookableMetadata' => new HookableMetadata(new HookMetadata($hookName, $context), $context, new ScalarDataBag([]), [$hookName]),
            'productName' => 'Mug',
        ]);

        $rendered = $component->refresh()->render()->toString();

        $this->assertStringContainsString('flag: yes', $rendered);
        $this->assertStringContainsString('product: Mug', $rendered);
        $this->assertStringContainsString('secret: none', $rendered);
        $this->assertStringContainsString('nested product: Mug', $rendered);
        $this->assertStringNotContainsString('s3cr3t', $rendered);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function render(string $path, array $context = []): string
    {
        /** @var Twig $twig */
        $twig = $this->getContainer()->get('twig');

        return $twig->render($path, $context);
    }
}
