# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - Unreleased

### Added

- Bundle third-party PHPStan extensions as direct dependencies
  (`phpstan-strict-rules`, `phpstan-deprecation-rules`, `phpstan-wordpress`, `phpstan-no-private`)
- DisallowQueryPostsRule: flag `query_posts()` usage
- PostTypeNameLengthRule: flag `register_post_type()` names exceeding 20 characters
- TaxonomyNameLengthRule: flag `register_taxonomy()` names exceeding 32 characters
- TransientExpirationRule: flag `set_transient()` without expiration
- PreSerializedDataRule: flag pre-serialized data passed to WP storage functions
- PreEncodedJsonDataRule: flag pre-encoded JSON data passed to WP storage functions
- NoDynamicCodeExecutionRule: flag `create_function()`, `assert()` with strings, `preg_replace()` with `/e`
- NoEvalRule: flag `eval()` usage
- UnsafeUnserializeRule: flag `unserialize()` without `allowed_classes`
- RemoteRequestTimeoutRule: flag `wp_remote_*` calls without explicit timeout
- NoHtmlDomParsingRule, NoHtmlDomParsingFuncCallRule, NoHtmlDomParsingNewRule: flag non-WP HTML parsing
- NoConcatenationInTranslationRule: flag string concatenation in translation functions
- NoBlanketSuppressionRule: flag `phpcs:disable`/`phpcs:ignore`/`@phpstan-ignore` without specific rules

### Fixed

- Bash syntax error in commit validation workflow (`^Merge\ ` regex)
- SerializedStringType and JsonEncodedStringType branded types for data-flow tracking
- SerializeReturnTypeExtension and JsonEncodeReturnTypeExtension type extensions

## [0.1.0] - Unreleased

### Added

- Initial project setup with CI/CD pipeline
- PHPStan extension structure with auto-discovery support
- PHPUnit test infrastructure for rule testing
- Coding standards enforcement via apermo-coding-standards
- GitHub Actions for CI, release automation, and PR validation
