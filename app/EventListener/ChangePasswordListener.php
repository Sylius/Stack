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

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\FrontUi\Event\ChangePasswordEvent;
use Sylius\FrontUi\Symfony\EventListener\Attribute\AsChangePasswordListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Webmozart\Assert\Assert;

#[AsChangePasswordListener]
final class ChangePasswordListener
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ChangePasswordEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        Assert::isInstanceOf($user, User::class);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $event->getNewPassword());

        $user->setPassword($hashedPassword);

        $this->entityManager->flush();
    }
}
