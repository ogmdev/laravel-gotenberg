# Create PDFs in Laravel apps using Gotenberg

[![Latest Version on Packagist](https://img.shields.io/packagist/v/safermobility/laravel-gotenberg.svg?style=flat-square)](https://packagist.org/packages/safermobility/laravel-gotenberg)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/safermobility/laravel-gotenberg/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/safermobility/laravel-gotenberg/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/safermobility/laravel-gotenberg/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/safermobility/laravel-gotenberg/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/safermobility/laravel-gotenberg.svg?style=flat-square)](https://packagist.org/packages/safermobility/laravel-gotenberg)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require safermobility/laravel-gotenberg
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-gotenberg-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-gotenberg-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-gotenberg-views"
```

## Usage

```php
$laravelGotenberg = new SaferMobility\LaravelGotenberg();
echo $laravelGotenberg->echoPhrase('Hello, SaferMobility!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [SaferMobility](https://github.com/safermobility)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
