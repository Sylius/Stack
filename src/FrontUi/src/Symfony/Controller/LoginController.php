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

namespace Sylius\FrontUi\Symfony\Controller;

use Sylius\FrontUi\Symfony\Form\Type\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class LoginController
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param class-string<FormTypeInterface>|null $formType
     */
    public function __invoke(Request $request, ?string $formType = null, ?string $template = null): Response
    {
        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $form = $this->formFactory->createNamed('', $formType ?? LoginType::class);

        return new Response($this->twig->render($template ?? '@SyliusFrontUi/security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'last_error' => $lastError,
        ]));
    }
}
