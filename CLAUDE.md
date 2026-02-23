# Apermo PHPStan WordPress Rules

## Project Overview

Custom PHPStan rules package for WordPress projects. Serves as the unified static analysis setup across all Apermo projects.

## Tech Stack

- **Language:** PHP 8.1+
- **Static Analysis:** PHPStan 2.0+ (level 6 with strict rules)
- **Testing:** PHPUnit 11 with PHPStan's RuleTestCase
- **Coding Standards:** apermo/apermo-coding-standards (PHPCS)
- **CI/CD:** GitHub Actions
- **License:** MIT

## Project Structure

```
src/
  Rules/         — Custom PHPStan rule classes
tests/
  Rules/         — Tests for custom rules (using RuleTestCase)
  data/          — PHP fixture files used by rule tests
  bootstrap.php  — PHPUnit bootstrap
rules.neon       — PHPStan extension config (auto-loaded by extension-installer)
phpstan.neon     — PHPStan config for analysing this package itself
phpunit.xml      — PHPUnit configuration
phpcs.xml.dist   — PHPCS configuration (uses Apermo standard)
```

## Conventions

### Writing Custom Rules

- Each rule class implements `PHPStan\Rules\Rule<T>` where T is the node type
- Place rules in `src/Rules/` with descriptive names
- Every rule must have a corresponding test in `tests/Rules/`
- Tests extend `PHPStan\Testing\RuleTestCase` and use fixture files from `tests/data/`
- Register every rule in `rules.neon`

### Naming

- Rule classes: `{Description}Rule.php` (e.g. `NoDirectDbQueryRule.php`)
- Test classes: `{Description}RuleTest.php`
- Test fixtures: descriptive kebab-case filenames in `tests/data/`
- Namespace: `Apermo\PhpStanWordPressRules\Rules\{ClassName}`
- Test namespace: `Apermo\PhpStanWordPressRules\Tests\Rules\{ClassName}`

### Code Style

- Enforced by apermo/apermo-coding-standards via PHPCS
- Use strict types in all PHP files: `declare(strict_types=1);`
- No unnecessary dependencies — keep the package lightweight

## Commands

- `composer test` — Run PHPUnit test suite
- `composer analyse` — Run PHPStan on this package
- `composer cs` — Run PHPCS coding standards check
- `composer cs-fix` — Auto-fix coding standards issues

## Important Notes

- This package is designed to be included via Composer in other projects
- The `rules.neon` file is the entry point for extension-installer integration
- WordPress stubs (`php-stubs/wordpress-stubs`) are available as dev dependency
- When adding rules, always register them in `rules.neon`
- CHANGELOG.md follows Keep a Changelog format — update it with every change
- Releases are automated via GitHub Actions based on CHANGELOG version headings
