# Ray.FormModule

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ray-di/Ray.FormModule/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.FormModule/?branch=1.x)
[![Code Coverage](https://scrutinizer-ci.com/g/ray-di/Ray.FormModule/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/ray-di/Ray.FormModule/?branch=1.x)
[![Build Status](https://travis-ci.org/ray-di/Ray.FormModule.svg?branch=1.x)](https://travis-ci.org/ray-di/Ray.FormModule)

## Installation

### Composer install

    $ composer require ray/form-module
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\AuraSqlModule\AuraSqlModule;
use Ray\AuraSqlModule\Annotation\AuraSqlConfig;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new FormModule);
    }
}
```
### Usage

TBD

### Demo

    $ php -S docs/demo/1.csrf/web.php

### Requirements

 * PHP 5.5+
 * hhvm

