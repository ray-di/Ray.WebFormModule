<?php
/**
 * This file is part of the Ray.ValidateModule package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Validation;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Ray\Di\AbstractModule;
use Ray\Validation\Annotation\Valid;

class ValidateModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        AnnotationRegistry::registerFile(dirname(__DIR__) . '/src-files/doctrine_annotations.php');
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Valid::class),
            [ValidateInterceptor::class]
        );
    }
}
