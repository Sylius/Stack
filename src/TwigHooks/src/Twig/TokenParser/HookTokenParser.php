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

namespace Sylius\TwigHooks\Twig\TokenParser;

use Sylius\TwigHooks\Twig\Node\HookNode;
use Twig\Node\Node;
use Twig\Node\Nodes;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class HookTokenParser extends AbstractTokenParser
{
    public const TAG = 'hook';

    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        if (method_exists($this->parser, 'parseExpression')) {
            $hooksNames = $this->parser->parseExpression();
        } else {
            // Remove when Twig 3.21 support is dropped
            $hooksNames = $this->parser->getExpressionParser()->parseExpression();
        }

        $hookContext = null;
        if ($stream->nextIf(Token::NAME_TYPE, 'with')) {
            if (method_exists($this->parser, 'parseExpression')) {
                $hookContext = $this->parseMultitargetExpression();
            } else {
                // Remove when Twig 3.21 support is dropped
                $hookContext = $this->parser->getExpressionParser()->parseMultitargetExpression();
            }
        }

        $only = false;
        if ($stream->nextIf(Token::NAME_TYPE, 'only')) {
            $only = true;
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        if (class_exists(\Twig\Node\Expression\FunctionNode\EnumCasesFunction::class)) {
            return new HookNode($hooksNames, $hookContext, $only, $lineno);
        }

        // Remove when twig < 3.12 support is dropped
        return new HookNode($hooksNames, $hookContext, $only, $lineno, $this->getTag());
    }

    public function getTag(): string
    {
        return self::TAG;
    }

    private function parseMultitargetExpression(): Nodes
    {
        $targets = [];
        while (true) {
            $targets[] = $this->parser->parseExpression();
            if (!$this->parser->getStream()->nextIf(Token::PUNCTUATION_TYPE, ',')) {
                break;
            }
        }

        return new Nodes($targets);
    }
}
