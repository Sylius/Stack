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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('sylius_twig_extra', [
        'twig_ux' => [
            'anonymous_component_template_prefixes' => [
                // <twig:sylius_shadcn_admin_ui:delete_modal>, <twig:sylius_shadcn_admin_ui:copyable>...
                'sylius_shadcn_admin_ui' => '@SyliusShadcnAdminUi/shared/components',
                // <twig:shadcn:Button>, <twig:shadcn:Card:Header>... (recipes copied from the UX Toolkit shadcn kit)
                'shadcn' => '@SyliusShadcnAdminUi/components/shadcn',
            ],
        ],
    ]);
};
