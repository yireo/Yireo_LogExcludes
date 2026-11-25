# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 25 November 2026
### Changed
- Settings moved to their own configuration section (`yireo_logexcludes/general/*`), protected by the ACL resource `Yireo_LogExcludes::config`; existing settings from `system/yireo_logexcludes/*` are migrated automatically
- The module is now disabled by default and no longer ships with a placeholder exclude pattern
- The plugin now only targets `Magento\Framework\Logger\Monolog` instead of a broader logger target
- Requires Monolog 3, and therefore Magento 2.4.8 or higher

### Fixed
- Errors could be hidden by an exclude pattern: records at or above a configurable minimum level (default ERROR) are no longer excluded, and EMERGENCY records never are
- Overly broad exclude patterns were accepted: patterns must now be 6 to 50 characters, and every change is logged with the name of the admin user who made it
- Prevent recursion when something is logged while the module configuration is still loading; configuration is now loaded only once per request

## [1.0.2] - 19 October 2026
### Fixed
- Read settings in default scope to fix setup:install
- Fix framework wildcard dep
- Fix "The default website isn't defined" during setup:install by reading config in default scope
- Respect the "enabled" setting when matching log messages

## [1.0.1] - 15 October 2025
### Fixed
- Move config path from dev/ to system/ to show it in Production Mode

## [1.0.0] - 15 October 2025
### Fixed
- Copy generic CI/CD files
- Adding all files to git via Yireo Command

