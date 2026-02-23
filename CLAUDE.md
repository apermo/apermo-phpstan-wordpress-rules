# Apermo PHPStan WordPress Rules

## Project Overview

Custom PHPStan rules package for WordPress projects. Serves as the unified static analysis setup across all Apermo projects.

## Tech Stack

- **Language:** PHP 8.1+
- **Static Analysis:** PHPStan 2.0+
- **Testing:** PHPUnit with PHPStan's RuleTestCase
- **License:** MIT

## Project Structure

```
src/
  Rules/         — Custom PHPStan rule classes
  Type/          — Custom type extensions (if needed)
tests/
  Rules/         — Tests for custom rules (using RuleTestCase)
  data/          — PHP fixture files used by rule tests
rules.neon       — PHPStan extension config (auto-loaded by extension-installer)
phpstan.neon     — PHPStan config for analysing this package itself
```

## Conventions

### Writing Custom Rules

- Each rule class implements `PHPStan\Rules\Rule<T>` where T is the node type
- Place rules in `src/Rules/` with descriptive names (e.g. `DisallowDirectDatabaseQueriesRule`)
- Every rule must have a corresponding test in `tests/Rules/`
- Tests extend `PHPStan\Testing\RuleTestCase` and use fixture files from `tests/data/`

### Naming

- Rule classes: `{Description}Rule.php` (e.g. `NoDirectDbQueryRule.php`)
- Test classes: `{Description}RuleTest.php`
- Test fixtures: descriptive kebab-case filenames in `tests/data/`

### Code Style

- Follow PSR-12
- Use strict types in all PHP files: `declare(strict_types=1);`
- No unnecessary dependencies — keep the package lightweight

## Commands

- `composer test` — Run test suite
- `composer analyse` — Run PHPStan on this package
- `composer cs` — Run code style checks (if configured)

## Important Notes

- This package is designed to be included via Composer in other projects
- The `rules.neon` file is the entry point for extension-installer integration
- WordPress stubs are needed for analysis — use `php-stubs/wordpress-stubs`
- When adding rules, always register them in `rules.neon`