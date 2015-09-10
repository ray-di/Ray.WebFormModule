<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Input\AntiCsrfInterface;

trait SetAntiCsrfTrait
{
    /**
     * @var AntiCsrfInterface
     */
    protected $antiCsrf;

    /**
     * @param AntiCsrfInterface $antiCsrf
     *
     * @\Ray\Di\Di\Inject
     */
    public function injectAntiCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->antiCsrf = $antiCsrf;
    }
}
