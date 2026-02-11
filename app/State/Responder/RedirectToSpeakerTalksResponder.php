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

namespace App\State\Responder;

use App\Entity\Speaker;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ResponderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class RedirectToSpeakerTalksResponder implements ResponderInterface
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @param Speaker[] $data
     */
    public function respond(mixed $data, Operation $operation, Context $context): RedirectResponse
    {
        Assert::allIsInstanceOf($data, Speaker::class);

        $ids = array_map(fn (Speaker $speaker) => $speaker->getId(), $data);

        $path = $this->router->generate('app_admin_talk_index', [
            'criteria' => [
                'speaker' => $ids,
            ],
        ]);

        return new RedirectResponse($path);
    }
}
