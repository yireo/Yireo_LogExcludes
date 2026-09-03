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
Navigate in the Magento Admin Panel to **Stores > Configuration > Advanced > Developer > Yireo LogExcludes** and configure the following:

- **Enable Log Filtering** (`system/yireo_logexcludes/enabled`)
- **Exclude Patterns** (`system/yireo_logexcludes/patterns`)


## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_LogExcludes/compile.yml?label=compile)
<!-- badges.test.end -->
