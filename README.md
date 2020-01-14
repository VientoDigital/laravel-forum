# Laravel Forum Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vientodigital/laravel-forum.svg?style=flat-square)](https://packagist.org/packages/vientodigital/laravel-forum)
[![Build Status](https://img.shields.io/travis/vientodigital/laravel-forum/master.svg?style=flat-square)](https://travis-ci.org/vientodigital/laravel-forum)
[![Quality Score](https://img.shields.io/scrutinizer/g/vientodigital/laravel-forum.svg?style=flat-square)](https://scrutinizer-ci.com/g/vientodigital/laravel-forum)
[![Total Downloads](https://img.shields.io/packagist/dt/vientodigital/laravel-forum.svg?style=flat-square)](https://packagist.org/packages/vientodigital/laravel-forum)

An easy to integrate forum to your laravel project. Just customize views, migrations routes and you are done.

## Installation

You can install the package via composer:

```bash
composer require vientodigital/laravel-forum
```

You could publish migrations, views & config

```bash
php artisan vendor:publish --provider="Vientodigital\LaravelForum\LaravelForumServiceProvider"
```

## Configuration

```php
//config/laravel-forum.php

/*
 * Customize table table names to your needs
 */
return [
    'table_names' => [
        'settings' => 'settings',
    ]
];
```

## Usage

---

```php
//routes/web.php

use Vientodigital\LaravelForum\LaravelForumFacade as LaravelForum;

Route::middleware(['auth'])->prefix('forum')->group(function () {
    LaravelForum::routes();
});
```

```php
//routes/api.php
use Vientodigital\LaravelForum\LaravelForumFacade as LaravelForum;

Route::middleware(['auth'])->prefix('forum')->group(function () {
    LaravelForum::routes();
});
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email victoryoalli@gmail.com instead of using the issue tracker.

## Credits

-   [Victor Yoalli](https://github.com/vientodigital)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
