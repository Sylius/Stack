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

namespace App\Form;

use App\Entity\Book;
use App\Enum\BookCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('authorName')
            ->add('category', EnumType::class, [
                'class' => BookCategory::class,
                'choice_value' => fn (?BookCategory $enum) => $enum?->value,
                'choice_label' => fn (BookCategory $choice) => ucfirst($choice->value),
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_resource';
    }
}
