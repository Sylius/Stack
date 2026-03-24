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

namespace Sylius\TwigHooks\Console\Command;

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

#[AsCommand(name: 'sylius:debug:twig-hooks', description: 'Debug twig hooks configuration.')]
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
                new InputArgument('name', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'One or more hook names'),
                new InputOption('all', 'a', InputOption::VALUE_NONE, 'Show all hookables including disabled ones'),
                new InputOption('config', 'c', InputOption::VALUE_NONE, 'Show hookables context, configuration and props'),
                new InputOption('tree', 't', InputOption::VALUE_NONE, 'Display hooks as a tree'),
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

To display the merged result of multiple hooks (as resolved at runtime):

    <info>php %command.full_name% sylius_admin.dashboard.index sylius_admin.common.index</info>

To include disabled hookables:

    <info>php %command.full_name% sylius_admin.product.index --all</info>

To show hookables context, configuration and props:

    <info>php %command.full_name% sylius_admin.product.index --config</info>

To display the full hooks hierarchy as a tree:

    <info>php %command.full_name% sylius_admin.common.create --tree</info>

To display the merged tree of multiple hooks (as resolved at runtime):

    <info>php %command.full_name% sylius_admin.dashboard.index sylius_admin.common.index --tree</info>
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
        /** @var array<string> $names */
        $names = array_unique((array) $input->getArgument('name'));
        /** @var bool $showAll */
        $showAll = $input->getOption('all');
        /** @var bool $showConfig */
        $showConfig = $input->getOption('config');
        /** @var bool $showTree */
        $showTree = $input->getOption('tree');

        if ($showTree && $showConfig) {
            $io->note('The --config option has no effect with --tree and will be ignored.');
        }

        $registeredHookNames = $this->hookablesRegistry->getHookNames();
        sort($registeredHookNames);

        // Multiple hooks — direct merge
        if (count($names) > 1) {
            $unknownNames = array_diff($names, $registeredHookNames);
            if (0 < count($unknownNames)) {
                $io->warning(sprintf('Hook(s) not found: "%s".', implode('", "', $unknownNames)));

                return Command::SUCCESS;
            }

            $io->title(implode(', ', $names));
            if ($showTree) {
                $this->displayHookTree($output, $names, $showAll);
            } else {
                $this->displayHookDetails($io, $names, $showAll, $showConfig);
            }

            return Command::SUCCESS;
        }

        // Single hook name
        if (1 === count($names)) {
            $singleName = $names[0];

            // Exact match
            if (in_array($singleName, $registeredHookNames, true)) {
                $io->title($singleName);
                if ($showTree) {
                    $this->displayHookTree($output, [$singleName], $showAll);
                } else {
                    $this->displayHookDetails($io, [$singleName], $showAll, $showConfig);
                }

                return Command::SUCCESS;
            }

            // Partial match (case-insensitive)
            $filteredHooks = array_filter(
                $registeredHookNames,
                static fn (string $hookName): bool => false !== stripos($hookName, $singleName),
            );

            if (0 === count($filteredHooks)) {
                $io->warning(sprintf('No hooks found matching "%s".', $singleName));

                return Command::SUCCESS;
            }

            if (1 === count($filteredHooks)) {
                $firstHook = reset($filteredHooks);
                $io->title($firstHook);
                if ($showTree) {
                    $this->displayHookTree($output, [$firstHook], $showAll);
                } else {
                    $this->displayHookDetails($io, [$firstHook], $showAll, $showConfig);
                }

                return Command::SUCCESS;
            }

            $this->displayHooksTable($io, $filteredHooks, $showAll);

            return Command::SUCCESS;
        }

        if (0 === count($registeredHookNames)) {
            $io->warning('No hooks registered.');

            return Command::SUCCESS;
        }

        $this->displayHooksTable($io, $registeredHookNames, $showAll);

        return Command::SUCCESS;
    }

    /**
     * @param array<string> $hookNames
     */
    private function displayHooksTable(SymfonyStyle $io, array $hookNames, bool $showAll): void
    {
        $rows = [];

        foreach ($hookNames as $hookName) {
            $hookables = $this->hookablesRegistry->getFor($hookName);
            $enabledCount = count(array_filter(
                $hookables,
                static fn (AbstractHookable $hookable): bool => !$hookable instanceof DisabledHookable,
            ));
            $disabledCount = count($hookables) - $enabledCount;

            $countDisplay = $showAll && $disabledCount > 0
                ? sprintf('%d (%d disabled)', count($hookables), $disabledCount)
                : (string) $enabledCount;

            $rows[] = [
                $hookName,
                $countDisplay,
            ];
        }

        $io->table(['Hook', 'Hookables'], $rows);
        $io->text(sprintf('Total: %d hooks', count($hookNames)));
    }

    /**
     * @param array<string> $hookNames
     */
    private function displayHookDetails(SymfonyStyle $io, array $hookNames, bool $showAll, bool $showConfig): void
    {
        $hookables = $showAll
            ? $this->hookablesRegistry->getFor($hookNames)
            : $this->hookablesRegistry->getEnabledFor($hookNames);

        if (0 === count($hookables)) {
            $io->warning(
                1 === count($hookNames)
                ? 'No hookables registered for this hook.'
                : 'No hookables registered for these hooks.',
            );

            return;
        }

        $headers = ['Name', 'Type', 'Target', 'Priority'];
        if ($showAll) {
            $headers[] = 'Status';
        }
        if ($showConfig) {
            $headers[] = 'Context';
            $headers[] = 'Configuration';
            $headers[] = 'Props';
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
                $row[] = $this->formatConfiguration($hookable->context);
                $row[] = $this->formatConfiguration($hookable->configuration);
                $row[] = $hookable instanceof HookableComponent
                    ? $this->formatConfiguration($hookable->props)
                    : '-';
            }

            $rows[] = $row;
        }

        $io->table($headers, $rows);
    }

    /**
     * @param array<string> $hookNames
     */
    private function displayHookTree(OutputInterface $output, array $hookNames, bool $showAll, string $prefix = ''): void
    {
        $hookables = $showAll
            ? $this->hookablesRegistry->getFor($hookNames)
            : $this->hookablesRegistry->getEnabledFor($hookNames);

        $childGroups = $this->getDirectChildHookGroups($hookNames);
        $hookablesList = array_values($hookables);
        $lastHookableIndex = count($hookablesList) - 1;

        foreach ($hookablesList as $index => $hookable) {
            $isLast = $index === $lastHookableIndex && 0 === count($childGroups);
            $connector = $isLast ? '└── ' : '├── ';

            $output->writeln($prefix . $connector . $this->formatHookableLine($hookable));
        }

        $childGroupsList = array_values($childGroups);
        foreach ($childGroupsList as $index => $childHookNames) {
            $isLast = $index === count($childGroupsList) - 1;
            $connector = $isLast ? '└── ' : '├── ';
            $childPrefix = $prefix . ($isLast ? '    ' : '│   ');

            $output->writeln(sprintf('%s%s<fg=cyan>(Hook)</> %s', $prefix, $connector, implode(', ', $childHookNames)));
            $this->displayHookTree($output, $childHookNames, $showAll, $childPrefix);
        }
    }

    private function formatHookableLine(AbstractHookable $hookable): string
    {
        $type = $this->getHookableType($hookable);
        $target = $this->getHookableTarget($hookable);
        $status = $hookable instanceof DisabledHookable ? ' <comment>[disabled]</comment>' : '';

        $coloredType = $hookable instanceof HookableComponent
            ? sprintf('<fg=yellow>(%s)</>', $type)
            : sprintf('<fg=green>(%s)</>', $type);

        return sprintf('%s [↑ %d] %s (%s)%s', $coloredType, $hookable->priority(), $hookable->name, $target, $status);
    }

    /**
     * @param array<string> $hookNames
     *
     * @return array<string, array<string>>
     */
    private function getDirectChildHookGroups(array $hookNames): array
    {
        $groups = [];
        $allHookNames = $this->hookablesRegistry->getHookNames();

        foreach ($hookNames as $hookName) {
            foreach ($allHookNames as $registeredName) {
                foreach (['.', '#'] as $separator) {
                    if (!str_starts_with($registeredName, $hookName . $separator)) {
                        continue;
                    }

                    $rest = substr($registeredName, strlen($hookName) + 1);
                    if (str_contains($rest, '.') || str_contains($rest, '#')) {
                        continue;
                    }

                    $groups[$separator . $rest][] = $registeredName;
                }
            }
        }

        ksort($groups);

        return $groups;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function formatConfiguration(array $configuration): string
    {
        if (0 === count($configuration)) {
            return '-';
        }

        return VarExporter::export($configuration);
    }

    private function getHookableType(AbstractHookable $hookable): string
    {
        return match (true) {
            $hookable instanceof HookableTemplate => 'Template',
            $hookable instanceof HookableComponent => 'Component',
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
