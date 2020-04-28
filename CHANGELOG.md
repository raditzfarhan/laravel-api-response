# Release Notes

All notable changes to Laravel API Response will be documented in this file.

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