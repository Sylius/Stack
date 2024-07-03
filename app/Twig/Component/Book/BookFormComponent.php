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

namespace App\Twig\Component\Book;

use App\Entity\Book;
use App\Form\BookType;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: '@SyliusBootstrapTheme/shared/crud/common/content/form.html.twig')]
final class BookFormComponent
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(fieldName: 'formData')]
    public ?Book $resource = null;

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(BookType::class, $this->resource);
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }
}
