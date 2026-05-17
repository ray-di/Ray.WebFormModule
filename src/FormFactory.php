<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use Aura\Filter\FilterFactory;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\Builder;

final class FormFactory
{
    /**
     * @psalm-param class-string<AbstractForm> $class
     * @phpstan-param class-string<AbstractForm> $class
     */
    public function newInstance(string $class): AbstractForm
    {
        $form = new $class();
        $form->setBaseDependencies(new Builder(), new FilterFactory(), new HelperLocatorFactory());
        $form->postConstruct();

        return $form;
    }
}
