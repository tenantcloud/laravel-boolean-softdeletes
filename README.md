# Laravel Boolean SoftDeletes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package is mostly for high load apps. It will speed up queries with soft deletes.
Boolean field is much better for indexing instead of unique timestamps.

## Install

Via Composer

``` bash
$ composer require tenantcloud/laravel-boolean-softdeletes
```

Add `Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean` trait to models with soft deletes.

Then create and run migration to add soft delete boolean field
```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_deleted')->default(0)->index();
});
```

If you want to use this package for existing project you can use built-in command
```dotenv
php artisan softdeletes:migrate
```

Also you can change default column name `is_deleted` to any other by setting static property `IS_DELETED`of certain model


Versions compatibility
``` bash
For Laravel 5 - laravel-boolean-softdeletes 0.1.2
For Laravel 6 - laravel-boolean-softdeletes 1.0.0
For Laravel 7 - laravel-boolean-softdeletes 2.0.0
For Laravel 8 - laravel-boolean-softdeletes 3.*
For Laravel 9 - laravel-boolean-softdeletes 4.*
```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email kolodiy.ivan.i@gmail.com instead of using the issue tracker.

## Credits

- [Ivan Kolodiy][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/tenantcloud/laravel-boolean-softdeletes.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/tenantcloud/laravel-boolean-softdeletes/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/tenantcloud/laravel-boolean-softdeletes.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/tenantcloud/laravel-boolean-softdeletes.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/tenantcloud/laravel-boolean-softdeletes.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/tenantcloud/laravel-boolean-softdeletes
[link-travis]: https://travis-ci.org/tenantcloud/laravel-boolean-softdeletes
[link-scrutinizer]: https://scrutinizer-ci.com/g/tenantcloud/laravel-boolean-softdeletes/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/tenantcloud/laravel-boolean-softdeletes
[link-downloads]: https://packagist.org/packages/tenantcloud/laravel-boolean-softdeletes
[link-author]: https://github.com/ivankolodii
[link-contributors]: ../../contributors
