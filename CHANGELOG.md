# Release Notes

All notable changes to Laravel API Response will be documented in this file.

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
