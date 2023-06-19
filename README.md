## Boom

Boom backend
Build on Laravel 9.x

## Project setup

+ Clone repo
+ `composer install --ignore-platform-req=ext-imagick`
+ `cp .env.example .env`
+ `./vendor/bin/sail build --no-cache`
+ `./vendor/bin/sail up -d`
+ `sail artisan key:generate`
+ `sail artisan migrate`
+ `sail artisan storage:link`
+ Make sure `storage/app/public` and `bootstrap/cache` is read and writable
+ Install requirements `sail artisan boom:install` or `sail artisan boom:install --refresh` \
For upload demonstration data, execute: `sail artisan boom:install-demo`

## Production
```bash
php artisan optimize
php artisan queue:listen
```
check number of imagick supported image format in production
```php
php --ri imagick
```
## Requirements

* Place values for (DB_DATABASE, DB_USERNAME, DB_PASSWORD) in your .env file
* Set up ```php artisan queue:listen``` with the supervisor. This is necessary for the correct operation of sending mail, notifications, deleting projects, accounts
* After restoring the postgresql database dump, execute the following command: `php artisan fix:postgresql-primary-keys-count`

## Dependencies
+ ffmpeg (for creation thumbnail by video)

## Info 
There are two endpoints with documentation: [Api v1 documentation](https://api.stage.boompp.com/documentation/api/v1) & [Mobile v1 documentation](https://api.stage.boompp.com/documentation/mobile/v1)

## Helper
Analyse project:
```bash
composer analyse
```
Check security, quality code
```bash
composer grumphp
```
Code style fixer 
```bash
composer pint
```

#### Translatable String Exporter for Laravel
`php artisan translatable:export <lang>` \
**Find untranslated strings in a language file** \
`php artisan translatable:inspect-translations no` \
To export translatable strings for a language and then inspect translations in it, use the following command \
`php artisan translatable:inspect-translations no --export-first` \
**Find untranslated strings in a language file (IDE)** \
`"([^"]*)": "\1"` (regex)
