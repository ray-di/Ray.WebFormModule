<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Ray\Di\AbstractModule;

class FormVndErrorModule extends AbstractModule
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->bind(FailureHandlerInterface::class)->to(VndErrorHandler::class);
    }
}
