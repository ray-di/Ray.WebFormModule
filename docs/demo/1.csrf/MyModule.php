<?php

namespace Ray\FormModule;

use Ray\Di\AbstractModule;

class MyModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new FormModule());
        $this->bind(FormInterface::class)->annotatedWith('contact_form')->to(ContactForm::class);
    }
}
