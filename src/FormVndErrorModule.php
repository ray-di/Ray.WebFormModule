<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Ray\Di\AbstractModule;

final class FormVndErrorModule extends AbstractModule
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->bind(FailureHandlerInterface::class)->to(VndErrorHandler::class);
    }
}
