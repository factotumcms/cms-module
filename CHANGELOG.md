# Changelog

All notable changes to this project will be documented in this file.

## [1.6.0] - 2025-12-03
### Added
- Added password history feeature
### Refactor
- Refactor media conversions logic
### Fixed
- Fix user's settings cache logic

## [1.5.0] - 2025-11-25
### Refactor
- Refactor settings logic
- Refactor language logic
- Refactor data binding logic
- Refactor installation command

### Added
- Added laravel query builder package

## [1.4.3] - 2025-10-23
### Fixed
- Rename avatar column in users table
- Fix existing migrations
- Fix workbench environment setup

## [1.4.2] - 2025-10-19
### Fixed
- Load avatar relation on user authentication

## [1.4.1] - 2025-10-18
### Changed
- Refactor media_id to avatar in users table migration

## [1.4.0] - 2025-10-17
### Updated
- General refactoring and code optimizations
- Changed apis routes prefix and removing context
- Removed make() methods from dtos and use constructors instead

## [1.3.2] - 2025-10-16
### Updated
- Refactor relation name from profile_picture to avatar

## [1.3.1] - 2025-10-10
### Updated
- Refactor store method on mediaService to return Media model instance.
### Removed
- Removed config/auth-providers.php

## [1.3.0] - 2025-10-09
### Added
- Implemented orchestra for feature testing
- Added enum, model and dto unit tests
- Added github action for running test on pull request
### Updated
- Updated readme file

## [1.2.0] - 2025-10-08
### Updated
- Changed media conversions logic.
- Removed filterable trait
- Change migration file name

### Added
- Added media conversions path setting.

## [1.1.0] - 2025-10-07
### Added
- Backoffice register user functionality.


## [1.0.0] - 2025-10-06
### Added
- Application initial release.
