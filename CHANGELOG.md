# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-05-19

### Added

- `Ray\WebFormModule\WebFormModule` — module class whose name matches the
  package and namespace. Use this in new code.

### Deprecated

- `Ray\WebFormModule\AuraInputModule` is now a thin subclass of
  `WebFormModule` kept for backwards compatibility. Existing applications
  that install `new AuraInputModule()` continue to work without changes,
  but should migrate to `new WebFormModule()`.

## [1.0.0] - 2026-05-17

### Changed

- Minimum PHP version raised to `8.0`.
- **BC break**: Migrated from Doctrine Annotations to PHP 8 Attributes. All
  validation metadata (`@FormValidation`, `@InputValidation`, `@VndError`) is
  now expressed with `#[FormValidation]`, `#[InputValidation]`, `#[VndError]`.
- **BC break**: CSRF protection for validation methods is now declared with
  the separate `#[CsrfProtection]` attribute. The previous `antiCsrf=true`
  boolean option on `@FormValidation` has been removed. CSRF checks are now
  opt-in: methods without `#[CsrfProtection]` perform no CSRF verification
  even if the form has an `AntiCsrf` object set.

  Before:

  ```php
  /**
   * @FormValidation(form="contactForm", antiCsrf=true)
   */
  public function createAction() {}
  ```

  After:

  ```php
  #[FormValidation(form: 'contactForm')]
  #[CsrfProtection]
  public function createAction() {}
  ```
- **BC break**: `AuraInputInterceptor`, `InputValidationInterceptor` and
  `VndErrorHandler` no longer accept a `Doctrine\Common\Annotations\Reader`
  in their constructors. Validation attributes are read directly via
  `ReflectionMethod::getAttributes()`.
- **BC break**: `FormInterface::input()` and `FormInterface::error()` now declare
  parameter and return types (`string $input`, `: string` respectively).
  Implementations must update their signatures.
- **BC break**: `ValidationException::__construct()` now declares parameter
  types (`string $message`, `int $code`, `Throwable|null $e`,
  `FormValidationError|null $error`). The `$error` property is now typed as
  `FormValidationError|null` via constructor property promotion.
- Added property type declarations and return types across the codebase to
  align with PHP 8 typing.
- Bumped dependencies: `ray/di` `^2.16`, `ray/aop` `^2.14`,
  `phpunit/phpunit` `^9.5`.

### Fixed

- `Exception\RuntimeException` now correctly extends `\RuntimeException`
  instead of `\LogicException`.
- `AntiCsrf::isValid()` uses strict comparison for the CSRF token.
- Eliminated PHP 8.4 deprecation warnings for implicit nullable parameters in
  `ValidationException::__construct()` and `VndErrorHandler::makeVndError()`.

### Added

- GitHub Actions workflows for tests and coding standards.
- `CHANGELOG.md`.
- `#[CsrfProtection]` attribute for composing CSRF checks with form/input
  validation attributes.

### Removed

- `doctrine/annotations` dependency.
- Travis CI configuration; replaced with GitHub Actions.

## [0.6.0] - 2018-05-27

See git history for changes prior to 1.0.0.

[1.0.1]: https://github.com/ray-di/Ray.WebFormModule/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/ray-di/Ray.WebFormModule/compare/0.6.0...1.0.0
[0.6.0]: https://github.com/ray-di/Ray.WebFormModule/releases/tag/0.6.0
