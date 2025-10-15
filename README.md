# Yireo LogExcludes

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

