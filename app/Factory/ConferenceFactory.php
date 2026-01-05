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

use App\Entity\Conference;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Conference>
 */
final class ConferenceFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Conference::class;
    }

    public function withName(string $name): self
    {
        return $this->with(['name' => $name]);
    }

    public function withStartingDate(\DateTimeImmutable $startsAt): self
    {
        return $this->with(['startsAt' => $startsAt]);
    }

    public function withEndingDate(\DateTimeImmutable $endsAt): self
    {
        return $this->with(['endsAt' => $endsAt]);
    }

    public function pastEvent(bool $pastEvent): self
    {
        return $this->with(['pastEvent' => $pastEvent]);
    }

    public function withArchivingDate(\DateTimeImmutable $archivedAt): self
    {
        return $this->with(['archivedAt' => $archivedAt]);
    }

    protected function defaults(): array|callable
    {
        return [
            'endsAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'name' => ucfirst(self::faker()->words(2, true)) . ' ' . self::faker()->year(),
            'pastEvent' => self::faker()->boolean(),
            'startsAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }
}
