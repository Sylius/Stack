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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class HtmlAttrTokensTest extends KernelTestCase
{
    use RendersTwigTrait;

    public function testHtmlAttrMergesTheSpaceSeparatedLists(): void
    {
        $html = $this->render(<<<'TWIG'
            {{ html_attr({'data-action': 'click->a#b'|html_attr_type('sst')}, {'data-action': 'keydown->c#d'|html_attr_type('sst')}) }}
            TWIG);

        self::assertSame('data-action="click-&gt;a#b keydown-&gt;c#d"', trim($html));
    }

    public function testHtmlAttrOmitsAnAttributeWhoseListIsFalse(): void
    {
        $html = $this->render(<<<'TWIG'
            {{ html_attr({'data-action': false|html_attr_type('sst'), 'data-x': 'y'}) }}
            TWIG);

        self::assertSame('data-x="y"', trim($html));
    }
}
