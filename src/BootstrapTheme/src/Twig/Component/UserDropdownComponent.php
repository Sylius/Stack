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

namespace Sylius\BootstrapTheme\Twig\Component;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(name: 'sylius_admin_ui:navbar:user_dropdown', template: '@SyliusBootstrapTheme/shared/components/navbar/user.html.twig')]
class UserDropdownComponent
{
    public function __construct(
        #[Autowire(param: 'sylius_admin_ui.routing')]
        private readonly array $routing,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    #[ExposeInTemplate(name: 'user')]
    public function getUser(): ?UserInterface
    {
        return $this->tokenStorage->getToken()?->getUser();
    }

    /**
     * @return array<array-key, array{title?: string, url?: string, icon?: string, type?: string, class?: string}>
     */
    #[ExposeInTemplate(name: 'menu_items')]
    public function getMenuItems(): array
    {
        return [
            [
                'title' => 'sylius.ui.logout',
                'url' => $this->routing['logout_path'] ?? '/logout',
                'icon' => 'logout',
            ],
        ];
    }
}
