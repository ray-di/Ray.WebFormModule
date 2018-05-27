<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Doctrine\Common\Annotations\Reader;
use Ray\Di\Di\Named;

class InputValidationInterceptor extends AuraInputInterceptor
{
    /**
     * @param Reader                  $reader
     * @param FailureHandlerInterface $handler
     *
     * @Named("handler=vnd_error")
     */
    public function __construct(Reader $reader, FailureHandlerInterface $handler)
    {
        parent::__construct($reader, $handler);
    }
}
