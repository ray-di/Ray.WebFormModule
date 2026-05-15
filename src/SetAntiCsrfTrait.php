<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;

/** @phpstan-ignore-next-line */
trait SetAntiCsrfTrait
{
    /** @\Ray\Di\Di\Inject */
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf): void
    {
        $this->antiCsrf = $antiCsrf;
    }
}
