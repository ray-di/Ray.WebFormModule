<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\WebFormModule;

use Doctrine\Common\Annotations\Reader;
use Ray\Di\Di\Named;

class InputValidationInterceptor extends AuraInputInterceptor
{
    /**
     * @var FailureHandlerInterface
     */
    protected $failureHandler;

    /**
     * @param Reader                  $reader
     * @param FailureHandlerInterface $handler
     *
     * @Named("handler=vnd_error")
     */
    public function __construct(Reader $reader, FailureHandlerInterface $handler)
    {
        $this->reader = $reader;
        $this->failureHandler = $handler;
    }
}
