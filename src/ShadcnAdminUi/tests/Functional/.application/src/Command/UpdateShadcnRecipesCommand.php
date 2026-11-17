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

namespace TestApplication\Sylius\ShadcnAdminUi\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

/**
 * Copies the shadcn kit recipes of Symfony UX Toolkit into the bundle (bundle development only).
 *
 * "ux:install" writes "templates/components/Button.html.twig" and "assets/controllers/dialog_controller.js"
 * for an application. The bundle keeps them in a "shadcn" sub-directory, so the recipes are installed
 * in a temporary directory, then rewritten for their new names before being copied:
 * - Twig components: <twig:Button> → <twig:shadcn:Button>, component('Button') → component('shadcn:Button');
 * - Stimulus controllers: "dialog" → "shadcn--dialog" (data-controller, actions, targets, values...).
 */
#[AsCommand('app:shadcn:update', 'Updates the shadcn kit recipes bundled in templates/components/shadcn and assets/controllers/shadcn')]
final class UpdateShadcnRecipesCommand extends Command
{
    private const PREFIX = 'shadcn';

    protected function configure(): void
    {
        $this->addArgument('recipes', InputArgument::IS_ARRAY, 'Recipes to update (e.g. "button dialog"), every bundled recipe by default');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $recipes = $input->getArgument('recipes');

        if (!$this->getApplication()?->has('ux:install')) {
            $io->error('The "ux:install" command is missing: run this command in the "dev" environment, with symfony/ux-toolkit installed.');

            return Command::FAILURE;
        }

        $bundleDir = \dirname(__DIR__, 5);
        $componentsDir = $bundleDir . '/templates/components/' . self::PREFIX;
        $controllersDir = $bundleDir . '/assets/controllers/' . self::PREFIX;

        $recipes = $recipes ?: $this->bundledRecipes($componentsDir, $bundleDir . '/vendor/symfony/ux-toolkit/kits/shadcn');
        $filesystem = new Filesystem();
        $tmpDir = $filesystem->tempnam(sys_get_temp_dir(), 'shadcn-recipes-');
        $filesystem->remove($tmpDir);

        try {
            foreach ($recipes as $recipe) {
                $output = new BufferedOutput();
                $status = $this->getApplication()->find('ux:install')->run(new ArrayInput([
                    'recipe' => $recipe,
                    '--kit' => 'shadcn',
                    '--destination' => $tmpDir,
                    '--force' => true,
                ]), $output);

                if (Command::SUCCESS !== $status) {
                    $io->error(\sprintf('Unable to install the "%s" recipe:', $recipe));
                    $io->writeln($output->fetch());

                    return Command::FAILURE;
                }
            }

            $controllers = $this->controllerIdentifiers($tmpDir . '/assets/controllers');
            $files = [
                ...$this->copy($tmpDir . '/templates/components', $componentsDir, $controllers, $filesystem),
                ...$this->copy($tmpDir . '/assets/controllers', $controllersDir, $controllers, $filesystem),
            ];
        } finally {
            $filesystem->remove($tmpDir);
        }

        $io->listing(array_map(static fn (string $file): string => Path::makeRelative($file, $bundleDir), $files));
        $io->success(\sprintf('%d recipe(s) updated. Review the git diff: local changes to the recipes must be restored by hand.', \count($recipes)));

        return Command::SUCCESS;
    }

    /**
     * Recipes whose main component is bundled: "AlertDialog.html.twig" → "alert-dialog".
     *
     * @return list<string>
     */
    private function bundledRecipes(string $componentsDir, string $kitDir): array
    {
        $recipes = [];

        foreach ((new Finder())->files()->in($componentsDir)->depth(0)->name('*.html.twig')->sortByName() as $file) {
            $recipe = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $file->getBasename('.html.twig')));

            if (is_dir($kitDir . '/' . $recipe)) {
                $recipes[] = $recipe;
            }
        }

        return $recipes;
    }

    /**
     * Stimulus identifiers of the installed controllers, longest first ("dropdown-menu-sub" before "dropdown-menu").
     *
     * @return list<string>
     */
    private function controllerIdentifiers(string $controllersDir): array
    {
        if (!is_dir($controllersDir)) {
            return [];
        }

        $identifiers = [];

        foreach ((new Finder())->files()->in($controllersDir)->name('*_controller.js') as $file) {
            $identifiers[] = str_replace('_', '-', $file->getBasename('_controller.js'));
        }

        usort($identifiers, static fn (string $a, string $b): int => \strlen($b) <=> \strlen($a));

        return $identifiers;
    }

    /**
     * @param list<string> $controllers
     *
     * @return list<string> Written files
     */
    private function copy(string $sourceDir, string $targetDir, array $controllers, Filesystem $filesystem): array
    {
        if (!is_dir($sourceDir)) {
            return [];
        }

        $written = [];

        foreach ((new Finder())->files()->in($sourceDir)->sortByName() as $file) {
            $target = $targetDir . '/' . $file->getRelativePathname();
            $filesystem->dumpFile($target, $this->rewrite($file->getContents(), $controllers));
            $written[] = $target;
        }

        return $written;
    }

    /**
     * @param list<string> $controllers
     */
    private function rewrite(string $content, array $controllers): string
    {
        $content = (string) preg_replace('/<(\/?)twig:(?=[A-Z])/', '<$1twig:' . self::PREFIX . ':', $content);
        $content = (string) preg_replace("/\\bcomponent\\((['\"])(?=[A-Z])/", 'component($1' . self::PREFIX . ':', $content);

        foreach ($controllers as $controller) {
            $id = preg_quote($controller, '/');
            $prefixed = self::PREFIX . '--' . $controller;

            // Actions ("click->dialog#open", "dialog#open") and Stimulus attributes ("data-dialog-target", "data-dialog-open-value"),
            // but not plain data attributes such as "data-collapsible-mode".
            $content = (string) preg_replace('/(?<![\w-])' . $id . '(?=#)/', $prefixed, $content);
            $content = (string) preg_replace('/(?<=data-)' . $id . '(?=-(?:target|[\w-]+-(?:value|class|outlet|param))\b)/', $prefixed, $content);
            // stimulus_controller('dialog'), stimulus_action('dialog', ...), stimulus_target('dialog', ...)
            $content = (string) preg_replace("/(stimulus_(?:controller|action|target)\\(['\"])" . $id . "(?=['\"])/", '$1' . $prefixed, $content);
            // data-controller="popover dialog" / 'data-controller': 'popover dialog'
            $content = (string) preg_replace_callback(
                "/(data-controller['\"]?\\s*[:=]\\s*['\"])([^'\"]*)/",
                static fn (array $match): string => $match[1] . preg_replace('/(?<![\w-])' . $id . '(?![\w-])/', $prefixed, $match[2]),
                $content,
            );
        }

        return $content;
    }
}
