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

namespace Sylius\FrontUi\Event;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class ChangePasswordEvent
{
    public function __construct(
        private PasswordAuthenticatedUserInterface $user,
        private string $newPassword,
    ) {
    }

    public function getUser(): PasswordAuthenticatedUserInterface
    {
        return $this->user;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
