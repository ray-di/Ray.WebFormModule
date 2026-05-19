---
name: migrate-to-1.0
description: Migrate a project consuming ray/web-form-module from 0.x (Doctrine Annotations) to 1.0 (PHP 8 Attributes). Detects @FormValidation / @InputValidation / @VndError annotations, antiCsrf=true options, AuraInputInterceptor Reader arguments, and FormInterface signature mismatches; rewrites them and reports remaining manual steps.
---

# Migrate ray/web-form-module 0.x → 1.0

Apply this skill in a project that depends on `ray/web-form-module` and is
upgrading from `0.x` to `1.0`. Run it from the project root.

## Scope

You will rewrite consumer code only. Do NOT touch files under
`vendor/`. Limit edits to:

- `src/`, `app/`, `tests/`, `lib/` and similar source roots
- PHP files only (`*.php`)

Skip generated/cache directories: `var/`, `cache/`, `tmp/`, `build/`,
`node_modules/`, `.git/`.

## Preflight

Before changing any file:

1. Confirm the project's `composer.json` declares `ray/web-form-module`.
   If not, abort and tell the user this skill does not apply.
2. Confirm the working tree is clean (`git status`). If not, ask the user
   whether to proceed — uncommitted changes will be mixed with this skill's
   edits.
3. Bump the constraint to `^1.0` in `composer.json` (`require` section). If
   `doctrine/annotations` is in `require` and is no longer used elsewhere,
   remove it.

## Step 1 — Rewrite validation annotations to attributes

For each PHP file under the source roots:

### 1a. `@FormValidation`

Find docblock annotations of the form:

```php
/**
 * @FormValidation(form="contactForm", onFailure="badRequestAction")
 */
public function createAction()
```

Rewrite to a native attribute on the method:

```php
#[FormValidation(form: 'contactForm', onFailure: 'badRequestAction')]
public function createAction()
```

Rules:

- Remove the annotation line from the docblock. If the docblock becomes
  empty (only `/**` and `*/`), delete the entire docblock.
- Convert `=` to `:` and double-quoted strings to single-quoted PHP strings.
- Preserve the existing `use Ray\WebFormModule\Annotation\FormValidation;`
  import. Add it if missing.

### 1b. `@FormValidation(... antiCsrf=true)` — **BC break**

The `antiCsrf` option was removed. Split it into two attributes:

```php
/**
 * @FormValidation(form="contactForm", antiCsrf=true)
 */
```

becomes

```php
#[FormValidation(form: 'contactForm')]
#[CsrfProtection]
```

Also add `use Ray\WebFormModule\Annotation\CsrfProtection;`.

If `antiCsrf=false` (or omitted), drop the option without adding
`#[CsrfProtection]` — methods without the attribute perform no CSRF check.

### 1c. `@InputValidation` and `@VndError`

Same treatment as `@FormValidation` (no `antiCsrf` concern).

```php
/** @InputValidation(form="form1") */
public function createAction($name) {}
```

```php
/**
 * @VndError(message="...", logref="a1000", path="/p", href={"_self"="/p"})
 */
```

become

```php
#[InputValidation(form: 'form1')]
public function createAction($name) {}
```

```php
#[VndError(message: '...', logref: 'a1000', path: '/p', href: ['_self' => '/p'])]
```

Note the `{...}` (Doctrine array literal) → `[...]` (PHP array) conversion
in `VndError`'s `href`.

## Step 2 — Drop `Reader` arguments

`AuraInputInterceptor`, `InputValidationInterceptor`, and `VndErrorHandler`
no longer accept a `Doctrine\Common\Annotations\Reader`.

Find direct `new` calls and DI module bindings such as:

```php
new AuraInputInterceptor($injector, $reader)
new InputValidationInterceptor($injector, $reader, $handler)
new VndErrorHandler($reader)
```

Drop the `Reader` argument:

```php
new AuraInputInterceptor($injector)
new InputValidationInterceptor($injector, $handler)
new VndErrorHandler()
```

If the project bound `Doctrine\Common\Annotations\Reader` in a Ray.Di
module solely to satisfy these constructors, remove that binding.

## Step 3 — Update `FormInterface` implementations

`FormInterface::input()` and `FormInterface::error()` now declare types:

```php
public function input(string $input): string;
public function error(string $input): string;
```

Any project class implementing `FormInterface` (directly, or via
`AbstractForm` override) must match these signatures. Update:

```php
public function input($input)
public function error($input)
```

to:

```php
public function input(string $input): string
public function error(string $input): string
```

## Step 4 — Update `ValidationException` callers

`ValidationException::__construct(string $message = '', int $code = 0,
?Throwable $e = null, ?FormValidationError $error = null)` — the third
parameter is now `Throwable|null` (was `Exception|null`) and `$error` is
typed `FormValidationError|null`.

If the project passes a non-`Throwable` value, fix the call site.

## Step 5 — Verify

After all rewrites:

1. Run the project's static analysis (`composer phpstan`, `composer psalm`,
   `composer cs` — whichever exist).
2. Run the project's test suite.
3. `grep -rn "@FormValidation\|@InputValidation\|@VndError\|antiCsrf" src/ tests/ app/ 2>/dev/null`
   should return nothing relevant.
4. `grep -rn "Doctrine\\\\Common\\\\Annotations\\\\Reader" src/ app/ 2>/dev/null`
   should not show usages tied to this package.

Report to the user:

- Files modified (count + paths).
- Any annotation occurrence you could not rewrite automatically (e.g.,
  uncommon formatting). List file:line so the user can finish manually.
- Static analysis / test results.

## Out of scope

- Migrating other libraries' annotations (only `Ray\WebFormModule\Annotation\*`).
- Renaming `SetAntiCsrfTrait` — the trait and `setAntiCsrf()` method are
  unchanged in 1.0.
- Deciding whether `#[CsrfProtection]` added by Step 1b is redundant. Forms
  that already use `SetAntiCsrfTrait` enforce CSRF via `AbstractForm::apply()`
  regardless of the attribute, so the attribute Step 1b inserts is harmless
  but unnecessary. Flag these to the user so they can drop the attribute if
  they prefer a single declaration site.
