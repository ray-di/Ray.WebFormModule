# Ray.ValidateModule

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ray-di/Ray.ValidateModule/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/ray-di/Ray.ValidateModule/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/ray-di/Ray.ValidateModule/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/ray-di/Ray.ValidateModule/?branch=develop)
[![Build Status](https://travis-ci.org/ray-di/Ray.ValidateModule.svg?branch=develop)](https://travis-ci.org/ray-di/Ray.ValidateModule)

## Installation

### Composer install

    $ composer require ray/validate-module
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\AuraSqlModule\AuraSqlModule;
use Ray\AuraSqlModule\Annotation\AuraSqlConfig;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new ValidateModule);
    }
}
```
### Usage

Annotate target method with `@Valid` annotation.

```php
use Ray\Validation\Annotation\Valid;

class User
{
    /**
     * @Valid
     */
    public function createUser($name)
    {
        // ...
    }
```

Provide `onValidate` prefixed name validation method in same class.

```php
    /**
     * @return ValidationResult
     */
    public function onValidateCreateUser($name)
    {
        $result = new ValidationResult;
        if (! is_string($name)) {
            $result->addError('name', 'name should be string');
        }

        return $result;
    }
```
Validate all parameters. If validation failed, `addError` with invalid parameter name and message.

`Ray\Validation\Exception\InvalidArgumentException` thrown on validation failed, But if you provide **OnInvalid** method with `onValidate` prefixed method, Alternative result is return.

```php
    public function onInvalidCreateUser(FailureInterface $failure)
    {

        // original parameters
        list($this->defaultName) = $failure->getInvocation()->getArguments();

        // errors
        foreach ($failure->getMessages() as $name => $messages) {
            foreach ($messages as $message) {
                echo "Input '{$name}': {$message}" . PHP_EOL;
            }
        }
    }
```

### Demo

    $ php docs/demo/run.php
    // It works!

### Requirements

 * PHP 5.4+
 * hhvm
 

