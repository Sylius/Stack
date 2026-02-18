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

namespace Tests\Sylius\TwigHooks\Unit\Console\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sylius\TwigHooks\Console\Command\DebugTwigHooksCommand;
use Sylius\TwigHooks\Hookable\AbstractHookable;
use Sylius\TwigHooks\Hookable\Merger\HookableMerger;
use Sylius\TwigHooks\Registry\HookablesRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandCompletionTester;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Sylius\TwigHooks\Utils\MotherObject\DisabledHookableMotherObject;
use Tests\Sylius\TwigHooks\Utils\MotherObject\HookableComponentMotherObject;
use Tests\Sylius\TwigHooks\Utils\MotherObject\HookableTemplateMotherObject;

final class DebugTwigHooksCommandTest extends TestCase
{
    public function testItDisplaysAllHooksSortedAlphabetically(): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with(['hookName' => 'sylius_shop.cart.summary', 'name' => 'items']),
            HookableTemplateMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'header']),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $display = $commandTester->getDisplay();

        $this->assertStringContainsString('Total: 2 hooks', $display);

        $adminPosition = strpos($display, 'sylius_admin.product.index');
        $shopPosition = strpos($display, 'sylius_shop.cart.summary');
        $this->assertLessThan($shopPosition, $adminPosition, 'Hooks should be sorted alphabetically');
    }

    public function testItDisplaysWarningWhenNoHooksRegistered(): void
    {
        $registry = $this->createRegistry([]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('No hooks registered', $commandTester->getDisplay());
    }

    public function testItDisplaysHookDetailsForExactMatchSortedByPriority(): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'header',
                'template' => '@SyliusAdmin/product/header.html.twig',
                'priority' => 100,
            ]),
            HookableComponentMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'grid',
                'component' => 'sylius_admin:product:grid',
                'priority' => 50,
            ]),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'sylius_admin.product.index']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $display = $commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/header\s+template\s+@SyliusAdmin\/product\/header\.html\.twig\s+100/', $display);
        $this->assertMatchesRegularExpression('/grid\s+component\s+sylius_admin:product:grid\s+50/', $display);

        $headerPosition = strpos($display, 'header');
        $gridPosition = strpos($display, 'grid');
        $this->assertLessThan($gridPosition, $headerPosition, 'Hookables should be sorted by priority (highest first)');
    }

    public function testItFiltersHooksByPartialNameCaseInsensitive(): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'header']),
            HookableTemplateMotherObject::with(['hookName' => 'sylius_admin.order.index', 'name' => 'header']),
            HookableTemplateMotherObject::with(['hookName' => 'sylius_shop.cart.summary', 'name' => 'items']),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'ADMIN']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $display = $commandTester->getDisplay();

        $this->assertStringContainsString('sylius_admin.product.index', $display);
        $this->assertStringContainsString('sylius_admin.order.index', $display);
        $this->assertStringNotContainsString('sylius_shop.cart.summary', $display);
    }

    public function testItDisplaysWarningWhenNoHooksMatchFilter(): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'header']),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'nonexistent']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('No hooks found matching "nonexistent"', $commandTester->getDisplay());
    }

    public function testItDisplaysDetailsWhenSingleHookMatchesFilter(): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'header',
                'template' => '@SyliusAdmin/product/header.html.twig',
            ]),
            HookableTemplateMotherObject::with(['hookName' => 'sylius_shop.cart.summary', 'name' => 'items']),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'product']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $display = $commandTester->getDisplay();

        $this->assertStringContainsString('sylius_admin.product.index', $display);
        $this->assertStringContainsString('@SyliusAdmin/product/header.html.twig', $display);
    }

    #[DataProvider('provideAllOptionCases')]
    public function testItHandlesDisabledHookablesBasedOnAllOption(
        bool $useAllOption,
        bool $shouldShowDisabled,
        bool $shouldShowStatusColumn,
    ): void {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'header',
            ]),
            DisabledHookableMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'disabled_item',
            ]),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'sylius_admin.product.index', '--all' => $useAllOption]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $display = $commandTester->getDisplay();

        $this->assertStringContainsString('header', $display);

        if ($shouldShowDisabled) {
            $this->assertStringContainsString('disabled_item', $display);
        } else {
            $this->assertStringNotContainsString('disabled_item', $display);
        }

        if ($shouldShowStatusColumn) {
            $this->assertStringContainsString('Status', $display);
        } else {
            $this->assertStringNotContainsString('Status', $display);
        }
    }

    /**
     * @return iterable<string, array{bool, bool, bool}>
     */
    public static function provideAllOptionCases(): iterable
    {
        yield 'without --all option' => [false, false, false];
        yield 'with --all option' => [true, true, true];
    }

    public function testItShowsConfigurationWithConfigOption(): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'header',
                'configuration' => [
                    'string_key' => 'value',
                    'boolean' => true,
                    'null_value' => null,
                ],
            ]),
            HookableTemplateMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'empty_config',
                'configuration' => [],
            ]),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'sylius_admin.product.index', '--config' => true]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $display = $commandTester->getDisplay();

        $this->assertStringContainsString('Configuration', $display);
        $this->assertStringContainsString('string_key', $display);
        $this->assertStringContainsString('true', $display);
        $this->assertStringContainsString('null', $display);
        $this->assertMatchesRegularExpression('/empty_config.*-/s', $display);
    }

    #[DataProvider('provideHookableCountCases')]
    public function testItDisplaysCorrectHookableCountInTable(bool $useAllOption, string $expectedPattern): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'header']),
            HookableTemplateMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'content']),
            DisabledHookableMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'disabled_item']),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['--all' => $useAllOption]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertMatchesRegularExpression($expectedPattern, $commandTester->getDisplay());
    }

    /**
     * @return iterable<string, array{bool, string}>
     */
    public static function provideHookableCountCases(): iterable
    {
        yield 'without --all shows only enabled count' => [false, '/sylius_admin\.product\.index\s+2\s/'];
        yield 'with --all shows total and disabled count' => [true, '/3 \(1 disabled\)/'];
    }

    public function testItDisplaysWarningWhenHookHasNoVisibleHookables(): void
    {
        $registry = $this->createRegistry([
            DisabledHookableMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'disabled_item']),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'sylius_admin.product.index']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('No hookables registered for this hook', $commandTester->getDisplay());
    }

    public function testItDisplaysDashForUnknownHookableTypeAndTarget(): void
    {
        $registry = $this->createRegistry([
            DisabledHookableMotherObject::with([
                'hookName' => 'sylius_admin.product.index',
                'name' => 'unknown_type',
            ]),
        ]);

        $commandTester = $this->createCommandTester($registry);
        $commandTester->execute(['name' => 'sylius_admin.product.index', '--all' => true]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertMatchesRegularExpression('/unknown_type\s+-\s+-/', $commandTester->getDisplay());
    }

    public function testItProvidesAutocompletion(): void
    {
        $registry = $this->createRegistry([
            HookableTemplateMotherObject::with(['hookName' => 'sylius_admin.product.index', 'name' => 'header']),
            HookableTemplateMotherObject::with(['hookName' => 'sylius_shop.cart.summary', 'name' => 'items']),
        ]);

        $command = new DebugTwigHooksCommand($registry);
        $completionTester = new CommandCompletionTester($command);

        $suggestions = $completionTester->complete(['']);

        $this->assertSame(['sylius_admin.product.index', 'sylius_shop.cart.summary'], $suggestions);
    }

    /**
     * @param array<AbstractHookable> $hookables
     */
    private function createRegistry(array $hookables): HookablesRegistry
    {
        return new HookablesRegistry($hookables, new HookableMerger());
    }

    private function createCommandTester(HookablesRegistry $registry): CommandTester
    {
        return new CommandTester(new DebugTwigHooksCommand($registry));
    }
}
