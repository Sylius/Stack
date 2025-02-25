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

use App\Entity\Talk;
use App\Repository\TalkRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(
    template: 'component/new_talks.html.twig',
)]
final class NewTalksComponent
{
    private const NUMBER_OF_TALKS = 5;

    public function __construct(
        private readonly TalkRepository $talkRepository,
    ) {
    }

    /**
     * @return Talk[]
     */
    #[ExposeInTemplate(name: 'new_talks')]
    public function getNewTalks(): array
    {
        return $this->talkRepository
            ->getLastTalks(self::NUMBER_OF_TALKS)
        ;
    }
}
