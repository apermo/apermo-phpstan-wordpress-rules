# Apermo PHPStan WordPress Rules

[![CI](https://github.com/apermo/apermo-phpstan-wordpress-rules/actions/workflows/ci.yml/badge.svg)](https://github.com/apermo/apermo-phpstan-wordpress-rules/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/apermo/apermo-phpstan-wordpress-rules/branch/main/graph/badge.svg)](https://codecov.io/gh/apermo/apermo-phpstan-wordpress-rules)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

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

## Development

### Setup

```bash
composer install
```

### Running Tests

```bash
composer test
```

### Static Analysis

```bash
composer analyse
```

### Coding Standards

```bash
composer cs
```

## License

MIT — see [LICENSE](LICENSE) for details.
