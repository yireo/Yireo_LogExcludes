# Yireo LogExcludes

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-log-excludes)
<!-- badges.specs.end -->


**A Magento 2 module that allows you to prevent certain messages from being written to log. Handy when there is a certain exception message triggered by bots without the need to investigate and without the need to have those messages flooding your logs.**

## Installation
```bash
composer require yireo/magento2-log-excludes
bin/magento module:enable Yireo_LogExcludes
```

Note that this module does **not** work together with the Yireo Whoops plugin.

## Configuration
Navigate in the Magento Admin Panel to **Stores > Configuration > Advanced > Yireo LogExcludes** and configure the following:

- **Allow Excluding Patterns from Logs** (`yireo_logexcludes/general/enabled`): Disabled by default.
- **Never Exclude From Level** (`yireo_logexcludes/general/min_level`): Log records of this level or higher are always written. Default is `ERROR`. Magento logs exceptions with the level `CRITICAL`, so to exclude exception messages, choose `ALERT` or `EMERGENCY`. Records with the level `EMERGENCY` are never excluded.
- **Exclude Patterns** (`yireo_logexcludes/general/patterns`): One pattern per line. A log message that contains a pattern anywhere is not written. Each pattern needs at least 6 characters, with a maximum of 50 patterns. Every change is logged as a notice, including the admin user that made it.

Access to this configuration section requires the ACL resource `Yireo_LogExcludes::config`.

Note that an attacker who knows a pattern can add it to their input to hide their own log messages. Keep patterns specific and keep the minimum level as low as possible.

## Upgrading from 1.0
The configuration paths moved from `system/yireo_logexcludes/*` to `yireo_logexcludes/general/*`. A data patch migrates existing values. Because the module is now disabled by default, installations that relied on the old default (enabled) need to enable the module again.

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/compile.yml?label=compile)
<!-- badges.test.end -->
