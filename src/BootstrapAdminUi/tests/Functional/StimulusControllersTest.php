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

namespace Tests\Sylius\BootstrapAdminUi\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\UX\StimulusBundle\AssetMapper\ControllersMapGenerator;

final class StimulusControllersTest extends KernelTestCase
{
    public function testItRegistersTheBundleControllersInTheApplicationStimulusApp(): void
    {
        /** @var ControllersMapGenerator $controllersMapGenerator */
        $controllersMapGenerator = self::getContainer()->get('stimulus.asset_mapper.controllers_map_generator');

        $controllersMap = $controllersMapGenerator->getControllersMap();

        $this->assertArrayHasKey('copy-to-clipboard', $controllersMap);
        $this->assertArrayHasKey('theme-switcher', $controllersMap);
        $this->assertSame(
            '@sylius/bootstrap-admin-ui/controllers/copy-to-clipboard-controller.js',
            $controllersMap['copy-to-clipboard']->asset->logicalPath,
        );
    }
}
