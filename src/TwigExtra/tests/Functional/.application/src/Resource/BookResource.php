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

namespace TestApplication\Sylius\TwigExtra\Resource;

use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Update;
use Sylius\Resource\Model\ResourceInterface;
use TestApplication\Sylius\TwigExtra\Form\BookResourceType;
use TestApplication\Sylius\TwigExtra\Grid\BookGrid;
use TestApplication\Sylius\TwigExtra\State\Provider\BookItemProvider;

#[AsResource(
    formType: BookResourceType::class,
    templatesDir: '@SyliusAdminUi/crud',
    driver: false,
    operations: [
        new Index(grid: BookGrid::class),
        new Create(),
        new Update(provider: BookItemProvider::class),
    ],
)]
final class BookResource implements ResourceInterface
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
