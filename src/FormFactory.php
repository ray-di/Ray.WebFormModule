<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;

final class FormFactory
{
    /**
     * @param string $class
     *
     * @phpstan-param class-string<AbstractForm> $class
     *
     * @psalm-param class-string<AbstractForm> $class
     */
    public function newInstance(string $class) : AbstractForm
    {
        $form = new $class();
        $form->setBaseDependencies(new Builder(), new FilterFactory(), new HelperLocatorFactory());
        $form->postConstruct();

        return $form;
    }
}
