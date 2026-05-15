<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Exception;

use Aura\Input\Exception\CsrfViolation;

final class CsrfViolationException extends CsrfViolation
{
}
