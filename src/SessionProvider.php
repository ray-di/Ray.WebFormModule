<?php
/**
 * This file is part of the Ray.FormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\FormModule;

use Aura\Session\SessionFactory;
use Ray\Di\ProviderInterface;

class SessionProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return (new SessionFactory)->newInstance($_COOKIE);
    }
}
