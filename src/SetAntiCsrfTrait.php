<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;
use Ray\Di\Di\Inject;

trait SetAntiCsrfTrait
{
    #[Inject]
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf): void
    {
        $this->antiCsrf = $antiCsrf;
    }
}
