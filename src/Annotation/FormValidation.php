<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule\Annotation;

use Attribute;

/**
 * @Annotation
 * @Target("METHOD")
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class FormValidation extends AbstractValidation
{
    /**
     * @var bool
     */
    public $antiCsrf = false;

    /**
     * Method name on validation failed.
     *
     * @var string
     */
    public $onFailure;

    public function __construct(
        string $form = 'form',
        bool $antiCsrf = false,
        ?string $onFailure = null
    ) {
        parent::__construct($form);
        $this->antiCsrf = $antiCsrf;
        $this->onFailure = $onFailure;
    }
}
