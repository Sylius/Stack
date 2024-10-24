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

namespace Sylius\FrontUi\Symfony\Controller\Account;

use Sylius\FrontUi\AccountEvents;
use Sylius\FrontUi\Event\ChangePasswordEvent;
use Sylius\FrontUi\Form\Model\Account\ChangePassword;
use Sylius\FrontUi\Symfony\Form\Type\UserChangePasswordType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

final class ChangePasswordController
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twig,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @param class-string<FormTypeInterface>|null $formType
     */
    public function __invoke(Request $request, ?string $formType = null, ?string $template = null, ?string $redirect = null): Response
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new \LogicException(sprintf('User must be authenticated and implements "%s".', PasswordAuthenticatedUserInterface::class));
        }

        $changePassword = new ChangePassword();
        $form = $this->formFactory->createNamed('', $formType ?? UserChangePasswordType::class, $changePassword);

        if (
            !$request->isMethodSafe() &&
            $form->handleRequest($request)->isSubmitted() &&
            $form->isValid()
        ) {
            $changePassword = $form->getData();

            $this->handleChangePassword($user, $changePassword);

            return new RedirectResponse($this->router->generate($redirect ?? 'sylius_front_ui_account_dashboard'));
        }

        return new Response($this->twig->render($template ?? '@SyliusFrontUi/account/change_password.html.twig', [
            'form' => $form->createView(),
        ]));
    }

    private function handleChangePassword(PasswordAuthenticatedUserInterface $user, ChangePassword $changePassword): void
    {
        $this->eventDispatcher->dispatch(new ChangePasswordEvent(
            $user,
            $changePassword->getNewPassword() ?? '',
        ), AccountEvents::CHANGE_PASSWORD);

        $this->addFlash('success', 'sylius.account.change_password');
    }

    private function addFlash(string $type, mixed $message): void
    {
        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException $e) {
            throw new \LogicException('You cannot use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".', 0, $e);
        }

        if (!$session instanceof FlashBagAwareSessionInterface) {
            throw new \LogicException(sprintf('You cannot use the addFlash method because class "%s" doesn\'t implement "%s".', get_debug_type($session), FlashBagAwareSessionInterface::class));
        }

        $session->getFlashBag()->add($type, $message);
    }
}
