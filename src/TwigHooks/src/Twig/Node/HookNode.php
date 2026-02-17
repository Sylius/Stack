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

namespace Sylius\TwigHooks\Twig\Node;

use Sylius\TwigHooks\Twig\Runtime\HooksRuntime;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Node;

#[YieldReady]
final class HookNode extends Node
{
    public function __construct(
        Node $name,
        ?Node $context,
        bool $only,
        int $lineno,
    ) {
        if (\func_num_args() > 4) {
            trigger_deprecation('sylius/twig-hooks', '0.11.0', \sprintf('The "tag" constructor argument of the "%s" class is deprecated and ignored (check which TokenParser class set it to "%s"), the tag is now automatically set by the Parser when needed.', static::class, func_get_arg(4) ?: 'null'));
        }

        // Remove when twig < 3.12 support is dropped
        if (!class_exists(\Twig\Node\Expression\FunctionNode\EnumCasesFunction::class)) {
            $tag = func_get_arg(4);

            parent::__construct(
                [
                    'name' => $name,
                    'hook_level_context' => $context ?? new ArrayExpression([], $lineno),
                ],
                [
                    'only' => $only,
                ],
                $lineno,
                $tag,
            );

            return;
        }

        parent::__construct(
            [
                'name' => $name,
                'hook_level_context' => $context ?? new ArrayExpression([], $lineno),
            ],
            [
                'only' => $only,
            ],
            $lineno,
        );
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        $compiler->raw(sprintf(
            '$hooksRuntime = $this->env->getRuntime(\'%s\');',
            HooksRuntime::class,
        ))->raw("\n");

        $compiler->raw(sprintf('%s $hooksRuntime->renderHook(', class_exists(YieldReady::class) ? 'yield' : 'echo'));
        $compiler->subcompile($this->getNode('name'));
        $compiler->raw(', ');
        $compiler->subcompile($this->getNode('hook_level_context'));
        $compiler->raw(', ');
        $compiler->raw('$context');
        $compiler->raw(', ');
        $compiler->raw($this->getAttribute('only') ? 'true' : 'false');
        $compiler->raw(");\n");
    }
}
