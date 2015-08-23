<?php
/**
 * This file is part of the Ray.FormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\FormModule;

use Aura\Session\Session;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class AuraSessionModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bind(Session::class)->toProvider(SessionProvider::class)->in(Scope::SINGLETON);
    }
}
