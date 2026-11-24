<?php

declare(strict_types=1);

// Builds the test application of the Sylius Stack as WebAssembly, with
// lyrixx/php-wasm-builder: PHP runs in a service worker, SQLite is the
// database, kept in IndexedDB.
//
//     castor demo:build    prepares the application, and builds PHP with it
//                          (--ui=bootstrap, --theme=zinc --theme=blue, --theme=none)
//     castor demo:serve    serves public/ on http://localhost:8000

defined('CASTOR_USE_CHDIR') || define('CASTOR_USE_CHDIR', true);

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use PhpWasm\Opcache;
use PhpWasm\Stack;
use PhpWasm\Target;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

use function Castor\context;
use function Castor\fs;
use function Castor\io;
use function Castor\run;
use function PhpWasm\build;

const ROOT = __DIR__ . '/..';
const EMBED = __DIR__ . '/build/embed';
const PUBLIC_DIR = __DIR__ . '/public';
const WASM_PHP_VERSION = '8.5.13';

// The dev dependencies of the monorepo that the application does not need in
// prod: the other ones are moved to "require"
const DEV_ONLY_PACKAGES = [
    'doctrine/doctrine-fixtures-bundle',
    'matthiasnoback/symfony-config-test',
    'matthiasnoback/symfony-dependency-injection-test',
    'phpstan/phpstan',
    'phpstan/phpstan-symfony',
    'phpunit/phpunit',
    'sylius-labs/coding-standard',
    'symfony/browser-kit',
    'symfony/css-selector',
    'symfony/debug-bundle',
    'symfony/dom-crawler',
    'symfony/maker-bundle',
    'symfony/ux-toolkit',
    'symfony/web-profiler-bundle',
    'symplify/monorepo-builder',
    'zenstruck/foundry',
];

function stack(): Stack
{
    return Stack::named('sylius-stack')
        ->php(WASM_PHP_VERSION)
        ->extensions('ctype', 'dom', 'fileinfo', 'filter', 'iconv', 'mbstring', 'pdo_sqlite', 'session', 'simplexml', 'tokenizer', 'xml', 'xmlreader', 'xmlwriter')
        ->memory('256mb')
        ->embed(EMBED, at: '/app')
        ->warmup('bin/console', 'cache:warmup')
        ->opcache(Opcache::Runtime)
        ->targets(Target::Web, Target::Node)
        ->outputDir(PUBLIC_DIR . '/build');
}

#[AsTask(name: 'embed', namespace: 'demo', description: 'Prepares the application to embed, in build/embed, and its public files, in public/')]
function embed(
    #[AsOption(description: 'The admin UI: shadcn or bootstrap', autocomplete: ['shadcn', 'bootstrap'])]
    string $ui = 'shadcn',
    #[AsOption(description: 'The color themes of shadcn, "none" for the neutral one', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, autocomplete: ['blue', 'green', 'orange', 'rose', 'slate', 'stone', 'violet', 'zinc', 'none'])]
    array $theme = ['rose'],
): void {
    if (!in_array($ui, ['shadcn', 'bootstrap'], true)) {
        throw new \InvalidArgumentException(\sprintf('The admin UI is "shadcn" or "bootstrap", not "%s".', $ui));
    }

    io()->section('The database, with the fixtures');
    $db = ROOT . '/var/demo.db';
    fs()->remove($db);
    $dev = context()
        ->withWorkingDirectory(ROOT)
        ->withEnvironment(['APP_ENV' => 'dev', 'DATABASE_URL' => 'sqlite:///%kernel.project_dir%/var/demo.db']);
    run(['php', 'bin/console', 'importmap:install'], context: $dev);
    run(['php', 'bin/console', 'doctrine:schema:create', '-q'], context: $dev);
    run(['php', 'bin/console', 'doctrine:fixtures:load', '-n', '-q'], context: $dev);

    io()->section('The application, without its dev dependencies');
    fs()->remove(EMBED);
    fs()->mkdir(EMBED);
    $mirror = static fn (string $path) => fs()->mirror(ROOT . "/{$path}", EMBED . "/{$path}");
    foreach (['app', 'assets', 'bin', 'config', 'templates', 'translations'] as $path) {
        $mirror($path);
    }
    // Only Foundry needs them, a dev dependency
    fs()->remove([EMBED . '/app/DataFixtures', EMBED . '/app/Factory', EMBED . '/app/Story']);
    // symfony.lock: Flex does not run the recipes again
    foreach (['importmap.php', 'symfony.lock'] as $path) {
        fs()->copy(ROOT . "/{$path}", EMBED . "/{$path}");
    }
    fs()->copy(ROOT . '/public/index.php', EMBED . '/public/index.php');
    foreach ((new Finder())->directories()->in(ROOT . '/src')->depth(0) as $package) {
        foreach (['assets', 'config', 'public', 'src', 'templates', 'translations'] as $path) {
            if (is_dir("{$package}/{$path}")) {
                $mirror("src/{$package->getFilename()}/{$path}");
            }
        }
    }
    fs()->mirror(__DIR__ . '/overlay', EMBED, options: ['override' => true]);
    // DoctrineBundle 3 does not have them anymore, and the monorepo never runs
    // in prod
    $doctrine = EMBED . '/config/packages/doctrine.yaml';
    fs()->dumpFile($doctrine, preg_replace('/^\s+(auto_generate_proxy_classes|proxy_dir):.*\n/m', '', file_get_contents($doctrine)));

    $composer = json_decode(file_get_contents(ROOT . '/composer.json'), true, flags: \JSON_THROW_ON_ERROR);
    foreach ($composer['require-dev'] as $package => $constraint) {
        if (!in_array($package, DEV_ONLY_PACKAGES, true)) {
            $composer['require'][$package] = $constraint;
        }
    }
    unset($composer['require-dev'], $composer['autoload-dev'], $composer['scripts']);
    $composer['autoload']['psr-4']['App\\'] = 'app/';
    $composer['config']['platform']['php'] = WASM_PHP_VERSION;
    fs()->dumpFile(EMBED . '/composer.json', json_encode($composer, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES) . "\n");

    $embed = context()->withWorkingDirectory(EMBED)->withEnvironment(['APP_ENV' => 'prod', 'APP_DEBUG' => '0']);
    run(['composer', 'update', '--no-dev', '--no-scripts', '--no-interaction', '--classmap-authoritative'], context: $embed);
    fs()->mirror(ROOT . '/assets/vendor', EMBED . '/assets/vendor');

    io()->section(\sprintf('The %s admin UI', $ui));
    // The script of the monorepo: the bundle, its configuration, the template
    // namespaces, and the Tailwind stylesheet of shadcn
    run(['bin/switch-admin-ui', $ui], context: $embed);
    if ('shadcn' === $ui) {
        run(['bin/switch-admin-theme', ...$theme], context: $embed);
    }

    io()->section('The public files, that the server serves, not PHP');
    // Iconify is called for the icons that are not local: PHP has no network
    // in the browser
    run(['php', 'bin/console', 'ux:icons:lock', '-q'], context: $embed);
    // ux:icons:lock only finds the ones of the templates: the menus and the
    // grids name theirs in PHP and in the configuration
    $icons = [];
    foreach ((new Finder())->files()->in([EMBED . '/app', EMBED . '/config', EMBED . '/src', EMBED . '/templates'])->name(['*.php', '*.yaml', '*.twig']) as $file) {
        // The usual sets, and any icon given to setIcon() or to an "icon" key
        preg_match_all('/[\'"]((?:tabler|lucide|mdi):[a-z0-9-]+)[\'"]/', $file->getContents(), $matches);
        array_push($icons, ...$matches[1]);
        preg_match_all('/(?:setIcon\(\s*|[\'"]?icon[\'"]?\s*(?:=>|:)\s*)[\'"]([a-z0-9-]+:[a-z0-9-]+)[\'"]/', $file->getContents(), $matches);
        array_push($icons, ...$matches[1]);
    }
    foreach (array_unique($icons) as $icon) {
        if (!is_file(EMBED . '/assets/icons/' . str_replace(':', '/', $icon) . '.svg')) {
            run(['php', 'bin/console', 'ux:icons:import', $icon, '-q'], context: $embed->withAllowFailure());
        }
    }
    run(['php', 'bin/console', 'assets:install', 'public', '--no-interaction', '-q'], context: $embed);
    run(['php', 'bin/console', 'tailwind:build', '--minify', '-q'], context: $embed);
    run(['php', 'bin/console', 'asset-map:compile', '-q'], context: $embed);
    foreach (['assets', 'bundles'] as $path) {
        fs()->remove(PUBLIC_DIR . "/{$path}");
        fs()->rename(EMBED . "/public/{$path}", PUBLIC_DIR . "/{$path}");
    }
    fs()->remove(PUBLIC_DIR . '/avatars');
    fs()->mirror(ROOT . '/public/avatars', PUBLIC_DIR . '/avatars');
    // What PHP reads of the compiled assets, to render the importmap
    fs()->mkdir(EMBED . '/public/assets');
    foreach ((new Finder())->files()->in(PUBLIC_DIR . '/assets')->depth(0)->name('*.json') as $file) {
        fs()->copy($file->getPathname(), EMBED . '/public/assets/' . $file->getFilename());
    }
    // Compiled in public/, and the cache is warmed up in PHP as WebAssembly,
    // with the paths it has there
    fs()->remove([EMBED . '/assets/vendor', EMBED . '/var']);
    fs()->remove((new Finder())->directories()->in(EMBED . '/src')->depth(1)->name(['assets', 'public']));

    // What is embedded is in the memory of PHP: only the English data of
    // symfony/intl, 27 MB with every locale
    fs()->remove((new Finder())->files()->in(EMBED . '/vendor/symfony/intl/Resources/data')->notName(['en.php', 'en_*.php', 'root.php', 'meta.php', '*.txt']));
    fs()->remove((new Finder())->directories()->in(EMBED . '/vendor')->depth('< 4')->name(['tests', 'Tests', 'doc', 'docs']));

    io()->section('The database, and the name of the build');
    // Keep in sync with public/sw.js
    fs()->copy($db, EMBED . '/data/demo.db');
    fs()->dumpFile(EMBED . '/data/build-id', bin2hex(random_bytes(8)));

    run(['du', '-sh', EMBED, EMBED . '/vendor']);
}

#[AsTask(name: 'build', namespace: 'demo', description: 'Builds PHP as WebAssembly, with the application')]
function build_task(
    #[AsOption(description: 'The admin UI: shadcn or bootstrap', autocomplete: ['shadcn', 'bootstrap'])]
    string $ui = 'shadcn',
    #[AsOption(description: 'The color themes of shadcn, "none" for the neutral one', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, autocomplete: ['blue', 'green', 'orange', 'rose', 'slate', 'stone', 'violet', 'zinc', 'none'])]
    array $theme = ['rose'],
    #[AsOption(description: 'Where the demo is in the URLs, "/Stack/" on the GitHub Pages of Sylius/Stack')]
    string $basePath = '/',
    #[AsOption(description: 'Given to "docker buildx bake --set", for a cache', mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)]
    array $set = [],
    #[AsOption(description: 'Do not prepare the application again')]
    bool $skipEmbed = false,
): void {
    if (!preg_match('{^/([a-zA-Z0-9._-]+/)*$}', $basePath)) {
        throw new \InvalidArgumentException(\sprintf('The base path starts and ends with a slash, as "/Stack/": "%s" does not.', $basePath));
    }
    if (!$skipEmbed) {
        embed($ui, $theme);
    }

    // A cache made on the host has its paths, and its 64-bit integers: PHP as
    // WebAssembly makes its own, with 32-bit ones
    fs()->remove(EMBED . '/var');

    // The web server serves the avatars, under the base path: vich_uploader_asset()
    // does not add it. Keep in sync with config/packages/vich_uploader.yaml
    $vich = EMBED . '/config/packages/vich_uploader.yaml';
    fs()->dumpFile($vich, preg_replace('{(uri_prefix:\s*)\S*(avatars/speakers)}', '${1}' . $basePath . '${2}', file_get_contents($vich)));

    io()->section('PHP as WebAssembly');
    build([stack()], $set);

    // php-web.mjs does not change when only the application does: the browser
    // updates the service worker when a script that it imports changes, and
    // fetches the .wasm again with another URL. Keep in sync with public/sw.js
    fs()->dumpFile(PUBLIC_DIR . '/build/version.js', \sprintf("export default '%s';\n", file_get_contents(EMBED . '/data/build-id')));

    // GitHub Pages answers a link to a page of the application, before the
    // service worker is there, with 404.html: the start page, whose relative
    // URLs are the ones of the root of the demo
    fs()->dumpFile(PUBLIC_DIR . '/404.html', str_replace('<head>', \sprintf("<head>\n    <base href=\"%s\">", htmlspecialchars($basePath)), file_get_contents(PUBLIC_DIR . '/index.html')));
}

#[AsTask(name: 'check', namespace: 'demo', description: 'Logs in, and checks that the pages of the admin panel answer, in PHP as WebAssembly, in node')]
function check(): void
{
    run(['node', 'check.mjs', PUBLIC_DIR . '/build/php-node.mjs']);
}

#[AsTask(name: 'serve', namespace: 'demo', description: 'Serves the demo on http://localhost:8000')]
function serve(): void
{
    io()->writeln('http://localhost:8000');
    run(['php', '-S', 'localhost:8000', '-t', PUBLIC_DIR], context: context()->withTimeout(null));
}
