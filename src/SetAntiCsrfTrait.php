<?php
/**
 * This file is part of the Ray.FormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\FormModule;

use Aura\Input\AntiCsrfInterface;

trait SetAntiCsrfTrait
{
    /**
     * @param AntiCsrfInterface $antiCsrf
     *
     * @\Ray\Di\Di\Inject
     */
    public function injectAntiCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->setAntiCsrf($antiCsrf);
    }
}
