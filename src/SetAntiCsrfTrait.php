<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;
use Ray\Di\Di\Inject;

trait SetAntiCsrfTrait
{
    #[Inject]
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf) : void
    {
        $this->antiCsrf = $antiCsrf;
    }
}
