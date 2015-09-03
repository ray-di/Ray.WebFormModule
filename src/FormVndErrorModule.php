<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Di\AbstractModule;

class FormVndErrorModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bind(FailureHandlerInterface::class)->to(VndErrorHandler::class);
    }
}
