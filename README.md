# Apermo PHPStan WordPress Rules

Custom PHPStan rules for WordPress projects. Provides a unified static analysis configuration and custom rules across all Apermo projects.

## Requirements

- PHP 8.1+
- PHPStan 2.0+

## Installation

```bash
composer require --dev apermo/phpstan-wordpress-rules
```

If you use [phpstan/extension-installer](https://github.com/phpstan/extension-installer), the rules are registered automatically.

### Manual Registration

Add to your `phpstan.neon`:

```neon
includes:
    - vendor/apermo/phpstan-wordpress-rules/rules.neon
```

## What's Included

### Custom Rules

*Rules will be added as the package evolves.*

### Baseline Configuration

This package provides a shared baseline PHPStan configuration for WordPress projects, including sensible defaults for WordPress plugin and theme development.

## Development

### Setup

```bash
composer install
```

### Running Tests

```bash
composer test
```

### Running PHPStan on Itself

```bash
composer analyse
```

## License

MIT — see [LICENSE](LICENSE) for details.