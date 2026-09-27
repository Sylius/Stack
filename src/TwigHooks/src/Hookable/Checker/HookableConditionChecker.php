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

namespace Sylius\TwigHooks\Hookable\Checker;

use Sylius\TwigHooks\Bag\DataBag;
use Sylius\TwigHooks\Hookable\AbstractHookable;
use Sylius\TwigHooks\Provider\Exception\InvalidExpressionException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class HookableConditionChecker implements HookableConditionCheckerInterface
{
    public function __construct(
        private readonly ExpressionLanguage $expressionLanguage,
        private readonly ?AuthorizationCheckerInterface $authorizationChecker = null,
        private readonly ?TokenStorageInterface $tokenStorage = null,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function isEnabled(AbstractHookable $hookable, array $context): bool
    {
        $condition = $hookable->condition;

        if (null === $condition) {
            return true;
        }

        $values = array_merge($context, [
            'user' => $this->tokenStorage?->getToken()?->getUser(),
            '_context' => new DataBag($context),
        ]);

        $expressionLanguage = clone $this->expressionLanguage;
        $expressionLanguage->register('is_granted', static fn (string ...$arguments): string => sprintf('is_granted(%s)', implode(', ', $arguments)), function (array $variables, string $attribute, mixed $subject = null): bool {
            if (null === $this->authorizationChecker) {
                return false;
            }

            return $this->authorizationChecker->isGranted($attribute, $subject);
        });

        try {
            return (bool) $expressionLanguage->evaluate(substr($condition, 2), $values);
        } catch (\Throwable $e) {
            throw new InvalidExpressionException(
                sprintf(
                    'Failed to evaluate the "%s" condition while rendering the "%s" hookable in the "%s" hook. Error: %s".',
                    $condition,
                    $hookable->name,
                    $hookable->hookName,
                    $e->getMessage(),
                ),
                previous: $e,
            );
        }
    }
}
