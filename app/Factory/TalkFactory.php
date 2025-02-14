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
use App\Entity\Speaker;
use App\Entity\Talk;
use App\Enum\Track;
use function Zenstruck\Foundry\lazy;
use Zenstruck\Foundry\Persistence\Exception\NotEnoughObjects;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;

/**
 * @extends PersistentProxyObjectFactory<Talk>
 */
final class TalkFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Talk::class;
    }

    public function withTitle(string $title): self
    {
        return $this->with(['title' => $title]);
    }

    public function withDescription(string $description): self
    {
        return $this->with(['description' => $description]);
    }

    /**
     * @param Proxy<Speaker>|Speaker ...$speakers
     */
    public function withSpeakers(Proxy|Speaker ...$speakers): self
    {
        return $this->with(['speakers' => $speakers]);
    }

    public function withStartingDate(\DateTimeImmutable $startsAt): self
    {
        return $this->with(['startsAt' => $startsAt]);
    }

    public function withEndingDate(\DateTimeImmutable $endsAt): self
    {
        return $this->with(['endsAt' => $endsAt]);
    }

    public function withTrack(Track $track): self
    {
        return $this->with(['track' => $track]);
    }

    /**
     * @param Proxy<Conference>|Conference $conference
     */
    public function withConference(Proxy|Conference $conference): self
    {
        return $this->with(['conference' => $conference]);
    }

    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(),
            'startsAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'endsAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'track' => self::faker()->randomElement(Track::cases()),
            'conference' => lazy(fn () => ConferenceFactory::randomOrCreate()),
            'speakers' => lazy(function () {
                try {
                    return SpeakerFactory::randomSet(1);
                } catch (NotEnoughObjects) {
                    return SpeakerFactory::new()->many(1);
                }
            }),
        ];
    }
}
