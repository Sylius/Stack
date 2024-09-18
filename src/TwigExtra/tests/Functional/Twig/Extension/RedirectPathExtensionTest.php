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

namespace Tests\Sylius\TwigExtra\Functional\Twig\Extension;

use Sylius\TwigExtra\Twig\Extension\RedirectPathExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class RedirectPathExtensionTest extends KernelTestCase
{
    private RedirectPathExtension $redirectPathExtension;

    protected function setUp(): void
    {
        $container = self::getContainer();

        $session = $container->get('session.factory')->createSession();
        $request = new Request();
        $request->setSession($session);
        $container->get('request_stack')->push($request);

        $this->redirectPathExtension = $container->get('sylius_twig_extra.twig.extension.redirect_path');
        $container->get('sylius.grid.filter_storage')->set(['criteria' => ['enabled' => true]]);
    }

    public function testItReturnsRedirectPathWithFiltersFromStorageApplied(): void
    {
        // Note, it requires an existing route path.
        $redirectPath = $this->redirectPathExtension->generateRedirectPath('/books');

        $this->assertSame('/books?criteria%5Benabled%5D=1', $redirectPath);
    }

    public function testItReturnsGivenPathIfRouteHasSomeMoreConfiguration(): void
    {
        // TODO ask internal core team member to reproduce this route with "some more configuration"
        $redirectPath = $this->redirectPathExtension->generateRedirectPath('/admin/ajax/products/search');

        $this->assertSame('/admin/ajax/products/search', $redirectPath);
    }

    public function testItReturnsGivenPathIfRouteIsNotMatched(): void
    {
        $redirectPath = $this->redirectPathExtension->generateRedirectPath('/invalid-path');

        $this->assertSame('/invalid-path', $redirectPath);
    }

    public function testItReturnsNullIfThePathIsNullAsWell(): void
    {
        $redirectPath = $this->redirectPathExtension->generateRedirectPath(null);

        $this->assertNull($redirectPath);
    }
}
