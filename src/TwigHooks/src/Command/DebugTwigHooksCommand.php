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

namespace Sylius\TwigHooks\Command;

use Sylius\TwigHooks\Hookable\AbstractHookable;
use Sylius\TwigHooks\Hookable\DisabledHookable;
use Sylius\TwigHooks\Hookable\HookableComponent;
use Sylius\TwigHooks\Hookable\HookableTemplate;
use Sylius\TwigHooks\Registry\HookablesRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarExporter\VarExporter;

#[AsCommand(name: 'sylius:debug:twig-hooks', description: 'Display hooks and their hookables')]
final class DebugTwigHooksCommand extends Command
{
    public function __construct(
        private readonly HookablesRegistry $hookablesRegistry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'A hook name or part of the hook name'),
                new InputOption('all', 'a', InputOption::VALUE_NONE, 'Show all hookables including disabled ones'),
                new InputOption('config', 'c', InputOption::VALUE_NONE, 'Show hookables configuration'),
            ])
            ->setHelp(
                <<<'EOF'
The <info>%command.name%</info> displays all Twig hooks in your application.

To list all hooks:

    <info>php %command.full_name%</info>

To filter hooks by name:

    <info>php %command.full_name% sylius_admin</info>

To get specific information about a hook:

    <info>php %command.full_name% sylius_admin.product.index</info>

To include disabled hookables:

    <info>php %command.full_name% sylius_admin.product.index --all</info>

To show hookables configuration:

    <info>php %command.full_name% sylius_admin.product.index --config</info>
EOF
            );
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestArgumentValuesFor('name')) {
            $suggestions->suggestValues($this->hookablesRegistry->getHookNames());
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        /** @var bool $showAll */
        $showAll = $input->getOption('all');
        /** @var bool $showConfig */
        $showConfig = $input->getOption('config');

        $hookNames = $this->hookablesRegistry->getHookNames();
        sort($hookNames);

        if (\is_string($name)) {
            // Exact match - show details
            if (\in_array($name, $hookNames, true)) {
                $this->displayHookDetails($io, $name, $showAll, $showConfig);

                return Command::SUCCESS;
            }

            // Partial match - filter and show table or details (case-insensitive)
            $filteredHooks = array_filter(
                $hookNames,
                static fn (string $hookName): bool => false !== stripos($hookName, $name),
            );

            if (0 === \count($filteredHooks)) {
                $io->warning(\sprintf('No hooks found matching "%s".', $name));

                return Command::SUCCESS;
            }

            if (1 === \count($filteredHooks)) {
                $this->displayHookDetails($io, reset($filteredHooks), $showAll, $showConfig);

                return Command::SUCCESS;
            }

            $this->displayHooksTable($io, $filteredHooks, $showAll);

            return Command::SUCCESS;
        }

        if (0 === \count($hookNames)) {
            $io->warning('No hooks registered.');

            return Command::SUCCESS;
        }

        $this->displayHooksTable($io, $hookNames, $showAll);

        return Command::SUCCESS;
    }

    /**
     * @param array<string> $hookNames
     */
    private function displayHooksTable(SymfonyStyle $io, array $hookNames, bool $showAll): void
    {
        $rows = [];

        foreach ($hookNames as $hookName) {
            $hookables = $this->hookablesRegistry->getAllFor($hookName);
            $enabledCount = \count(array_filter(
                $hookables,
                static fn (AbstractHookable $hookable): bool => !$hookable instanceof DisabledHookable,
            ));
            $disabledCount = \count($hookables) - $enabledCount;

            $countDisplay = $showAll && $disabledCount > 0
                ? \sprintf('%d (%d disabled)', \count($hookables), $disabledCount)
                : (string) $enabledCount;

            $rows[] = [
                $hookName,
                $countDisplay,
            ];
        }

        $io->table(['Hook', 'Hookables'], $rows);
        $io->text(\sprintf('Total: %d hooks', \count($hookNames)));
    }

    private function displayHookDetails(SymfonyStyle $io, string $hookName, bool $showAll, bool $showConfig): void
    {
        $io->title($hookName);

        $hookables = $this->hookablesRegistry->getAllFor($hookName);
        if (!$showAll) {
            $hookables = array_filter(
                $hookables,
                static fn (AbstractHookable $hookable): bool => !$hookable instanceof DisabledHookable,
            );
        }

        if (0 === \count($hookables)) {
            $io->warning('No hookables registered for this hook.');

            return;
        }

        $headers = ['Name', 'Type', 'Target', 'Priority'];
        if ($showAll) {
            $headers[] = 'Status';
        }
        if ($showConfig) {
            $headers[] = 'Configuration';
        }

        $rows = [];
        foreach ($hookables as $hookable) {
            $row = [
                $hookable->name,
                $this->getHookableType($hookable),
                $this->getHookableTarget($hookable),
                $hookable->priority(),
            ];

            if ($showAll) {
                $row[] = $hookable instanceof DisabledHookable ? 'disabled' : 'enabled';
            }

            if ($showConfig) {
                $row[] = $this->formatConfiguration($hookable->configuration);
            }

            $rows[] = $row;
        }

        $io->table($headers, $rows);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function formatConfiguration(array $configuration): string
    {
        if (0 === \count($configuration)) {
            return '-';
        }

        return VarExporter::export($configuration);
    }

    private function getHookableType(AbstractHookable $hookable): string
    {
        return match (true) {
            $hookable instanceof HookableTemplate => 'template',
            $hookable instanceof HookableComponent => 'component',
            default => '-',
        };
    }

    private function getHookableTarget(AbstractHookable $hookable): string
    {
        return match (true) {
            $hookable instanceof HookableTemplate => $hookable->template,
            $hookable instanceof HookableComponent => $hookable->component,
            default => '-',
        };
    }
}
