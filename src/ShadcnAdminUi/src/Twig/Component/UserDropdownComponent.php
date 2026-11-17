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

namespace Sylius\ShadcnAdminUi\Twig\Component;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * Same contract as Sylius\BootstrapAdminUi\Twig\Component\UserDropdownComponent:
 * decorate or replace the service to add your own entries (profile, settings...).
 */
class UserDropdownComponent
{
    /**
     * @param array<string, string> $routing
     */
    public function __construct(
        private readonly array $routing,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RouterInterface $router,
    ) {
    }

    #[ExposeInTemplate(name: 'user')]
    public function getUser(): ?UserInterface
    {
        return $this->tokenStorage->getToken()?->getUser();
    }

    /**
     * @return array<array-key, array{title?: string, url?: string, icon?: string, type?: string, class?: string, variant?: string}>
     */
    #[ExposeInTemplate(name: 'menu_items')]
    public function getMenuItems(): array
    {
        return [
            [
                'title' => 'sylius.ui.logout',
                'url' => $this->routing['logout_path'] ?? $this->router->generate('sylius_admin_ui_logout'),
                'icon' => 'lucide:log-out',
            ],
        ];
    }
}
