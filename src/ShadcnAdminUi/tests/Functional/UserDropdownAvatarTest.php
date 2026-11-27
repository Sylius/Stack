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

namespace Tests\Sylius\ShadcnAdminUi\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Sylius\ShadcnAdminUi\Twig\Component\UserDropdownComponent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\TwigComponent\ComponentFactory;
use Symfony\UX\TwigComponent\ComponentRenderer;

final class UserDropdownAvatarTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testTheDefaultComponentDisplaysInitials(): void
    {
        self::bootKernel();
        self::getContainer()->get('security.token_storage')->setToken(new UsernamePasswordToken(
            new InMemoryUser('jane@example.com', null),
            'main',
        ));

        $crawler = $this->crawl("{{ component('sylius_shadcn_admin_ui:navbar:user_dropdown') }}");

        self::assertCount(0, $crawler->filter('[data-slot="avatar-image"]'));
        self::assertSame('ja', trim($crawler->filter('[data-slot="avatar-fallback"]')->text()));
    }

    #[DataProvider('avatarUrls')]
    public function testAReplacementComponentCanProvideAnAvatar(?string $url): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $tokenStorage = $container->get('security.token_storage');
        $tokenStorage->setToken(new UsernamePasswordToken(new InMemoryUser('jane@example.com', null), 'main'));
        $component = new class([], $tokenStorage, $container->get('router'), $url) extends UserDropdownComponent {
            public function __construct(
                array $routing,
                TokenStorageInterface $tokenStorage,
                RouterInterface $router,
                private readonly ?string $url,
            ) {
                parent::__construct($routing, $tokenStorage, $router);
            }

            protected function resolveAvatarUrl(UserInterface $user): ?string
            {
                return $user->getUserIdentifier() === 'jane@example.com' ? $this->url : null;
            }
        };
        /** @var ComponentFactory $componentFactory */
        $componentFactory = $container->get('ux.twig_component.component_factory');
        /** @var ComponentRenderer $componentRenderer */
        $componentRenderer = $container->get('ux.twig_component.component_renderer');

        $html = $componentRenderer->render($componentFactory->mountFromObject(
            $component,
            [],
            $componentFactory->metadataFor('sylius_shadcn_admin_ui:navbar:user_dropdown'),
        ));
        $crawler = new Crawler($html);
        $tokenStorage->setToken(null);
        self::assertNull($component->getAvatarUrl());
        $fallback = $crawler->filter('[data-slot="avatar-fallback"]');
        self::assertSame('ja', trim($fallback->text()));
        self::assertNotContains('hidden', explode(' ', (string) $fallback->attr('class')));
        $image = $crawler->filter('[data-slot="avatar-image"]');
        if (null === $url || '' === $url) {
            self::assertCount(0, $image);

            return;
        }

        self::assertSame($url, $image->attr('src'));
        self::assertSame('', $image->attr('alt'));
        self::assertContains('hidden', explode(' ', (string) $image->attr('class')));
        self::assertStringContainsString('avatar-fallback', (string) $image->attr('onload'));
        self::assertStringContainsString('&quot;', $html);
    }

    public static function avatarUrls(): iterable
    {
        yield 'null' => [null];
        yield 'empty' => [''];
        yield 'image' => ['/avatar.jpg?name="Jane"&size=32'];
    }

    public function testNoUserHasNoAvatarOrMenu(): void
    {
        self::bootKernel();
        $component = self::getContainer()->get('sylius_shadcn_admin_ui.twig.component.navbar.user_dropdown');

        self::assertNull($component->getAvatarUrl());
        self::assertCount(0, $this->crawl("{{ component('sylius_shadcn_admin_ui:navbar:user_dropdown') }}")->filter('[data-slot="avatar"]'));
    }
}
