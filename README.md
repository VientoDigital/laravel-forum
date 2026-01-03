# Laravel Forum Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vientodigital/laravel-forum.svg?style=flat-square)](https://packagist.org/packages/vientodigital/laravel-forum)
[![Tests](https://github.com/VientoDigital/laravel-forum/actions/workflows/tests.yml/badge.svg)](https://github.com/VientoDigital/laravel-forum/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/VientoDigital/laravel-forum/branch/main/graph/badge.svg)](https://codecov.io/gh/VientoDigital/laravel-forum)
[![Total Downloads](https://img.shields.io/packagist/dt/vientodigital/laravel-forum.svg?style=flat-square)](https://packagist.org/packages/vientodigital/laravel-forum)

An easy to integrate forum to your laravel project. Just customize views, migrations routes and you are done.

## Requirements

- PHP ^8.2
- Laravel 11.x or 12.x
- Livewire ^3.0

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
