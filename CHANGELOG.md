# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.4] - 2022-05-10
### Added
- New alternative layout for sites list, works better for sites with longer names (resolves issue #26).
- Improved security and privacy: Local Google fonts, session handling for API and CSRF checks.

### Fixed
- Issue #27: Daylight Savings Not Showing (when OWM API is not used).
- Improved API error reporting (Issue #25).
- Generate unique hashes for JS/CSS filenames via webpack so updated assets are downloaded quickly after upgrading.

## [1.1.3] - 2022-03-23
### Added
- Issue #20: Added option within sites.json to open links in a new tab.

### Fixed
- Typo in readme, corrected "OWPAPIKEY" to "OWMAPIKEY" in Open Weather Map section.

## [1.1.2] - 2022-03-17
### Added
- Show alternative 12 hour clock format using the "ampmclock" option.

### Fixed
- Fix issue #15: Properly encode and escape URLs with query params.
- Fix issue #16. UTC timezone shift was being multiplied by 1000 every 10 seconds.

## [1.1.1] - 2022-03-17
### Fixed
- Metrictemp option was not passed to page template.
- Corrected some typos in readme and comments.

## [1.1.0] - 2022-03-16
### Added
- Sites can be categorised using tags in sites.json.
- Friendly greeting can be disabled using the "showgreeting" config option.
- Background brightness and blur can be customised using the "bgbright" and "bgblur" config options.

### Fixed
- Initial page load is no longer stalled while favicons are retrieved and cached.
- Calls to OpenWeather API are proxied via server so API key is not exposed to client.

## [1.0.3] - 2022-02-21
### Added
- New weather description and temperature display in bottom right of page.
- Option to show/hide clock (SHOWCLOCK).
- Option to switch between metric and imperial temperature (METRICTEMP).
- Global defaults in sites.json for nofollow and icon.
- Jump now has a favicon!

### Fixed
- Clock will now show correct time where local time zone is not the same as UTC.