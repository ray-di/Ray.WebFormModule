<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;

trait SetAntiCsrfTrait
{
    /** @param AntiCsrfInterface $antiCsrf */
    #[\Ray\Di\Di\Inject]
    public function setAntiCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->antiCsrf = $antiCsrf;
    }
}
