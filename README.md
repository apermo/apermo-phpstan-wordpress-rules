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

### Bundled Extensions

This package automatically includes:

- [phpstan-strict-rules](https://github.com/phpstan/phpstan-strict-rules)
- [phpstan-deprecation-rules](https://github.com/phpstan/phpstan-deprecation-rules)
- [phpstan-wordpress](https://github.com/szepeviktor/phpstan-wordpress)
- [phpstan-no-private](https://github.com/swissspidy/phpstan-no-private)

### Custom Rules

| Rule | Category | What it detects |
|------|----------|-----------------|
| DisallowQueryPostsRule | wp-api | `query_posts()` usage |
| PostTypeNameLengthRule | wp-api | `register_post_type()` name > 20 chars |
| TaxonomyNameLengthRule | wp-api | `register_taxonomy()` name > 32 chars |
| TransientExpirationRule | wp-api | `set_transient()` without expiration |
| RemoteRequestTimeoutRule | wp-api | `wp_remote_*` without explicit timeout |
| PreSerializedDataRule | data-integrity | Pre-serialized data in WP storage functions |
| PreEncodedJsonDataRule | data-integrity | Pre-encoded JSON in WP storage functions |
| NoDynamicCodeExecutionRule | security | `create_function()`, `assert()` with string, `preg_replace()` with `/e` |
| NoEvalRule | security | `eval()` usage |
| UnsafeUnserializeRule | security | `unserialize()` without `allowed_classes` |
| NoHtmlDomParsingRule | code-quality | `DOMDocument::loadHTML`, tidy functions, `Masterminds\HTML5` |
| NoConcatenationInTranslationRule | i18n | String concatenation inside `__()`, `_e()`, etc. |
| NoBlanketSuppressionRule | code-quality | `phpcs:disable`/`phpcs:ignore`/`@phpstan-ignore` without specific rules |

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
