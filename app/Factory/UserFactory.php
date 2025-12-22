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

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    public function withEmail(string $email): self
    {
        return $this->with(['email' => $email]);
    }

    public function admin(): self
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }

    public function withPassword(string $password): self
    {
        return $this->with(['password' => $password]);
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->email(),
            'password' => self::faker()->password(),
            'roles' => [],
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword() ?? ''));
            })
        ;
    }
}
