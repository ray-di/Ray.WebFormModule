<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Exception;

use Aura\Input\Exception\CsrfViolation;

class CsrfViolationException extends CsrfViolation
{
}
