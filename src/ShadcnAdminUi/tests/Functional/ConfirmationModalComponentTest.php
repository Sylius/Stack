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

final class ConfirmationModalComponentTest extends KernelTestCase
{
    use RendersTwigTrait;

    private const FOOTER_BUTTONS = '[data-slot="alert-dialog-footer"] button';

    public function testItOnlyRendersTheCancelButtonByDefault(): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:confirmation_modal title="Archive?" />');

        self::assertCount(1, $crawler->filter(self::FOOTER_BUTTONS));
        self::assertSame('shadcn--alert-dialog#close', $crawler->filter(self::FOOTER_BUTTONS)->attr('data-action'));
    }

    public function testItRendersAConfirmButtonThatClosesTheDialogBeforeItsOwnAction(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            <twig:sylius_shadcn_admin_ui:confirmation_modal
                title="Archive?"
                confirm_label="Archive"
                :confirm_attributes="{'data-action': 'live#action', 'data-live-action-param': 'archive'}"
            />
            TWIG);

        $confirm = $crawler->filter(self::FOOTER_BUTTONS)->last();

        self::assertCount(2, $crawler->filter(self::FOOTER_BUTTONS));
        self::assertSame('Archive', trim($confirm->text()));
        self::assertSame('button', $confirm->attr('type'));
        self::assertSame('shadcn--alert-dialog#close live#action', $confirm->attr('data-action'));
        self::assertSame('archive', $confirm->attr('data-live-action-param'));
        self::assertNotNull($confirm->attr('data-test-confirm-button'));
    }

    public function testItRendersADestructiveConfirmButton(): void
    {
        $crawler = $this->crawl('<twig:sylius_shadcn_admin_ui:confirmation_modal confirm_label="Delete" confirm_variant="destructive" />');

        self::assertStringContainsString('bg-destructive', (string) $crawler->filter(self::FOOTER_BUTTONS)->last()->attr('class'));
    }

    public function testItCanSubmitAFormOfThePage(): void
    {
        $crawler = $this->crawl(<<<'TWIG'
            <twig:sylius_shadcn_admin_ui:confirmation_modal
                confirm_label="Publish"
                :confirm_attributes="{type: 'submit', form: 'publish-form', name: 'publish'}"
            />
            TWIG);

        $confirm = $crawler->filter(self::FOOTER_BUTTONS)->last();

        self::assertSame('submit', $confirm->attr('type'));
        self::assertSame('publish-form', $confirm->attr('form'));
        self::assertSame('publish', $confirm->attr('name'));
        self::assertSame('shadcn--alert-dialog#close', $confirm->attr('data-action'));
    }
}
