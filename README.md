# WIP

# Ray.ValidateModule

## A standard PHP project skeleton

## Installation

### Composer install

    $ composer require ray/validation-module
 
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

Annotate target method with `@Valid`

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

Provide validate `onValidate` prefixed name for validation.

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
Add error if validation failed.

`Ray\Validation\Exception\InvalidArgumentException` thrown, but if you provide **OnInvalid** method with `onValidate` prefixed method,
Alternative result is return.

```php
    public function onInvalidCreateUser(FailureInterface $failure)
    {
        $error = '';
        foreach ($failure->getMessages() as $name => $messages) {
            foreach ($messages as $message) {
                $error .= "Input '{$name}': {$message}" . PHP_EOL;
            }
        }

        return $error;
    }
```

### Demo

    $ php docs/demo/run.php
    // It works!

### Requirements

 * PHP 5.4+
 * hhvm
 

