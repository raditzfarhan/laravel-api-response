# Release Notes

All notable changes to Laravel API Response will be documented in this file.

## Version 1.3.3 (2026-04-11)

### Changed
- `validationError()` now accepts an optional `$message` parameter, allowing callers to pass `$e->getMessage()` from `ValidationException` instead of the default `"Validation error."` string.

## Version 1.3.2 (2026-04-11)

### Fixed
- Fix `response()->api()` macro registration by replacing `ResponseFactory` with `Response` facade.

## Version 1.3.1 (2026-04-09)

### Added
- Auto-register `response()->api()` macro via the service provider.

### Removed
- Drop Lumen support.

## Version 1.3.0 (2026-04-09)

### Added
- Add support for Laravel 12 and 13.
- Add publishable config file for global key renaming and global response fields.
- Add `code` attribute for application-level error/status codes.
- Add `headers()` method for custom HTTP response headers.
- Add shorthand methods: `methodNotAllowed`, `notAcceptable`, `requestTimeout`, `conflict`, `gone`, `tooManyRequests`.
- Add PHPDoc `@method` annotations to `ApiResponse` class and facade for IDE autocomplete.
- Add test suite using Orchestra Testbench.

### Fixed
- Fix singleton state leak: `ApiResponse` is now resolved via `bind` instead of `singleton`, preventing stale payload across requests in long-lived processes.

### Changed
- Replace `krsort`-based payload ordering with an explicit `$attributeOrder` array for predictable key order.

### Removed
- Drop support for Laravel 7 and 8.
- Drop support for PHP 7.4.

## Version 1.2.0 (2024-03-05)

### Added
- Add support for Laravel 11.

## Version 1.1.0 (2023-03-21)

### Added
- Add support for Laravel 9 and 10.
- Add new shortcut methods `notImplemented`, `badGateway`, `serviceUnavailable`, `gatewayTimeout`.

### Changed
- Replace all http code to use `Response` class.

### Removed
- Drop support for Laravel 6.

## Version 1.0.5 (2021-10-12)

### Added
- Add support for PHP 8.

## Version 1.0.4 (2021-08-19)

### Added
- Add i18n support.
- Add en and ms translation.

## Version 1.0.3 (2020-05-09)

### Changed
- Fix bug bad request http status.
- Update readme.

## Version 1.0.2 (2020-04-28)

### Changed
- Add support for resource collection when using collection method.
- Reformat meta into meta and links.

### Added
- Add default empty array for data attribute when using collection method.
- Add badRequest, unauthorized, forbidden, notFound, internalServerError and commonError method.

## Version 1.0.1 (2020-04-22)

### Changed
- Rearrange response attributes to be in order.
- Fix bug when setting attribute.

### Added
- Add created method.
- Add validationError method.
- Add collection method.

## Version 1.0.0 (2020-04-17)

### Added
- Initial commit.
- Add Service Provider.
- Add ApiResponse class.
- Add dynamic method call and dynamic setter and getter to ApiResponse class.
- Add success() and failed() method to ApiResponse class.
- Define allowed attributes in ApiResponse class.
