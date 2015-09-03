<?php

namespace Ray\WebFormModule;

use Ray\Di\AbstractModule;

class FakeVndErrorModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new FormVndErrorModule);
        $this->install(new FakeModule);
    }
}
