# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [5.0.2] - 2023-03-13

### Changed

- Migrate PHPUnit config file to PHPUnit 10

## [5.0.1] - 2023-03-13

### Changed

- Update shivammathur/setup-php GitHub action dependency to v2

## [5.0.0] - 2023-03-13

### Added

- Laravel 10 support

### Removed

- Laravel 8 support as it is EOL

## [4.0.0] - 2022-04-05

### Added

- Laravel 9 support
- PHP 7.3 as a dependency as Laravel 8 still supports it

### Changed

- PHPUnit configuration
- Analysis script alias
  
### Removed

- Laravel 5, 6 and 7 support as they are no longer officially supported
  
## [3.1.0] - 2020-09-30

### Added

- Added Laravel 8 support thanks to @annejan
  
## [3.0.0] - 2020-03-05

### Added

- Added Laravel 7 support
  
## [2.0.0] - 2020-01-02

### Added

- Sitemaps support 🤩
- 'allow' path directive support.

### Changed

- Refactored a whole lot of code in the inner working, including the configuration of the sitemap paths. If you are upgrading from v1 to v2, please see UPGRADE.MD.
- Namespace now matches the Github vendor name.

## [1.1.0] - 2017-01-16

### Changed

- Changed Github/vendor username from gverschuur to verschuur. (The namespace in the code is still the same for now).

## [1.0.0] - 2016-10-04

### Added

- Everything for initial release :shipit:
