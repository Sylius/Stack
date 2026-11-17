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

use TestApplication\Sylius\ShadcnAdminUi\Kernel;

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'project_dir' => dirname(__DIR__),
];

if (file_exists(dirname(__DIR__, 4) . '/vendor/autoload_runtime.php')) {
    require_once dirname(__DIR__, 4) . '/vendor/autoload_runtime.php';
} else {
    require_once dirname(__DIR__, 6) . '/vendor/autoload_runtime.php';
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
