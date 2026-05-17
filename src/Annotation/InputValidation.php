<?php

declare(strict_types=1);

namespace Ray\WebFormModule\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class InputValidation extends AbstractValidation
{
}
