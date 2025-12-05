<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;

trait SetAntiCsrfTrait
{
    /** @\Ray\Di\Di\Inject */
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->antiCsrf = $antiCsrf;
    }
}
