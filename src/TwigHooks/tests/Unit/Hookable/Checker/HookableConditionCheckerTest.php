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

namespace Tests\Sylius\TwigHooks\Unit\Hookable\Checker;

use PHPUnit\Framework\TestCase;
use Sylius\TwigHooks\Hookable\Checker\HookableConditionChecker;
use Sylius\TwigHooks\Hookable\Checker\HookableConditionCheckerInterface;
use Sylius\TwigHooks\Hookable\HookableTemplate;
use Sylius\TwigHooks\Provider\Exception\InvalidExpressionException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\Sylius\TwigHooks\Utils\MotherObject\HookableTemplateMotherObject;

final class HookableConditionCheckerTest extends TestCase
{
    public function testItReturnsTrueWhenNoConditionIsDefined(): void
    {
        $hookable = HookableTemplateMotherObject::some();

        $this->assertTrue($this->createTestSubject()->isEnabled($hookable, []));
    }

    public function testItReturnsTrueWhenConditionIsSatisfied(): void
    {
        $hookable = new HookableTemplate('some_hook', 'some_name', 'some_target', condition: '@=_context.user !== null');

        $this->assertTrue($this->createTestSubject()->isEnabled($hookable, ['user' => new \stdClass()]));
    }

    public function testItReturnsFalseWhenConditionIsNotSatisfied(): void
    {
        $hookable = new HookableTemplate('some_hook', 'some_name', 'some_target', condition: '@=_context.user !== null');

        $this->assertFalse($this->createTestSubject()->isEnabled($hookable, ['user' => null]));
    }

    public function testItResolvesConditionAgainstContextVariable(): void
    {
        $hookable = new HookableTemplate('some_hook', 'some_name', 'some_target', condition: '@=_context.user !== null');

        $this->assertTrue($this->createTestSubject()->isEnabled($hookable, ['user' => new \stdClass()]));
    }

    public function testItEvaluatesIsGrantedCondition(): void
    {
        $hookable = new HookableTemplate('some_hook', 'some_name', 'some_target', condition: '@=is_granted("ROLE_ADMIN")');
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects($this->once())->method('isGranted')->with('ROLE_ADMIN', null)->willReturn(true);

        $checker = new HookableConditionChecker(new ExpressionLanguage(), $authorizationChecker);

        $this->assertTrue($checker->isEnabled($hookable, []));
    }

    public function testItResolvesUserFromSecurityToken(): void
    {
        $hookable = new HookableTemplate('some_hook', 'some_name', 'some_target', condition: '@=user !== null');
        $user = $this->createMock(UserInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        $checker = new HookableConditionChecker(new ExpressionLanguage(), tokenStorage: $tokenStorage);

        $this->assertTrue($checker->isEnabled($hookable, []));
    }

    public function testItKeepsContextVariableReservedWhenContextContainsCollidingKey(): void
    {
        $hookable = new HookableTemplate('some_hook', 'some_name', 'some_target', condition: '@=_context.user !== null');

        $this->assertTrue($this->createTestSubject()->isEnabled($hookable, ['_context' => 'unexpected', 'user' => new \stdClass()]));
    }

    public function testItThrowsExceptionWhenConditionCannotBeEvaluated(): void
    {
        $hookable = new HookableTemplate('some_hook', 'some_name', 'some_target', condition: '@=this is not a valid expression !!!');

        $this->expectException(InvalidExpressionException::class);

        $this->createTestSubject()->isEnabled($hookable, []);
    }

    private function createTestSubject(): HookableConditionCheckerInterface
    {
        return new HookableConditionChecker(new ExpressionLanguage());
    }
}
