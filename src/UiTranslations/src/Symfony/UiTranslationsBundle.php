<?php

declare(strict_types=1);

namespace Sylius\UiTranslations\Symfony;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class UiTranslationsBundle extends AbstractBundle
{
    public function getPath(): string
    {
        if (!isset($this->path)) {
            $reflected = new \ReflectionObject($this);
            $this->path = \dirname($reflected->getFileName(), 3);
        }

        return $this->path;
    }
}
