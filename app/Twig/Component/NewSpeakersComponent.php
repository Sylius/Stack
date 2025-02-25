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

namespace App\Twig\Component;

use App\Entity\Speaker;
use App\Repository\SpeakerRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(
    template: 'component/new_speakers.html.twig',
)]
final class NewSpeakersComponent
{
    private const NUMBER_OF_SPEAKERS = 5;

    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
    ) {
    }

    /**
     * @return Speaker[]
     */
    #[ExposeInTemplate(name: 'new_speakers')]
    public function getNewSpeakers(): array
    {
        return $this->speakerRepository
            ->getLastSpeakers(self::NUMBER_OF_SPEAKERS)
        ;
    }
}
