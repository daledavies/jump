# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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