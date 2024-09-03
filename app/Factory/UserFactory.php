<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
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

    public function withPassword(string $password): self
    {
        return $this->with(['password' => $password]);
    }

    /** @param string[] $roles */
    public function withRoles(array $roles): self
    {
        return $this->with(['roles' => $roles]);
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
            ->afterInstantiate(function(User $user): void {
                $hashedPassword = $this->userPasswordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
            })
        ;
    }
}
