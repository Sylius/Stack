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

use App\Entity\Speaker;
use App\Entity\SpeakerAvatar;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Speaker>
 */
final class SpeakerFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Speaker::class;
    }

    public function withFirstName(string $firstName): self
    {
        return $this->with(['firstName' => $firstName]);
    }

    public function withLastName(string $lastName): self
    {
        return $this->with(['lastName' => $lastName]);
    }

    public function withCompanyName(string $companyName): self
    {
        return $this->with(['companyName' => $companyName]);
    }

    public function withAvatar(string $avatar): self
    {
        return $this->with(['avatar' => $avatar]);
    }

    protected function defaults(): array|callable
    {
        return [
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $attributes): array {
                if (is_string($attributes['avatar'] ?? null)) {
                    $avatarPath = $attributes['avatar'];

                    $tmpDir = sys_get_temp_dir();

                    $basename = basename($avatarPath);
                    (new Filesystem())->copy($avatarPath, $tmpDir . \DIRECTORY_SEPARATOR . $basename);

                    $avatar = new SpeakerAvatar();
                    $avatar->setFile(
                        new UploadedFile(
                            path: $tmpDir . \DIRECTORY_SEPARATOR . $basename,
                            originalName: $basename,
                            test: true,
                        ),
                    );

                    $attributes['avatar'] = $avatar;
                }

                return $attributes;
            })
        ;
    }
}
