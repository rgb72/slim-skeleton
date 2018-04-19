# Slim skeleton

## System Requirement
- PHP 5.6
- Mysql 5.6+
- Composer

## Getting Started
Install package with composer

```
composer install
```
this follow files and folders will be created after install

```
├─ .env
└─ storage
    ├─ cache
    │   ├─ templates
    │   └─ variables
    ├─ database
    ├─ logs
    └─ report
```
Don't forget to change __environment variables__ in `.env`

## Console

To use in command-line interface

```
php run {command}
```

## Tests

We use phpunit for automate test

```
composer test

```
