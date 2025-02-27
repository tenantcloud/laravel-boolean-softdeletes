# Laravel Boolean SoftDeletes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

This package is designed for high-load applications and optimizes queries with soft deletes by utilizing a boolean field for indexing, which is more efficient than using unique timestamps.

## Install

Via Composer

```bash
$ composer require tenantcloud/laravel-boolean-softdeletes
```

Add `Webkid\LaravelBooleanSoftdeletes\SoftDeletesBoolean` trait to models with soft deletes.

Then create and run migration to add soft delete boolean field

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_deleted')->default(false)->index();
});
```

If you want to use this package for existing project you can use built-in command

```dotenv
php artisan softdeletes:migrate
```

Also you can change default column name `is_deleted` to any other by setting static property `IS_DELETED`of certain model

Versions compatibility

```bash
For Laravel 9 - laravel-boolean-softdeletes 4.*
For Laravel 10 - laravel-boolean-softdeletes 5.*
For Laravel 11 - laravel-boolean-softdeletes 6.*
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email kolodiy.ivan.i@gmail.com instead of using the issue tracker.

## Credits

-   [Ivan Kolodiy][link-author]
-   [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/tenantcloud/laravel-boolean-softdeletes.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/tenantcloud/laravel-boolean-softdeletes.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/tenantcloud/laravel-boolean-softdeletes.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/tenantcloud/laravel-boolean-softdeletes
[link-downloads]: https://packagist.org/packages/tenantcloud/laravel-boolean-softdeletes
[link-author]: https://github.com/ivankolodii
[link-contributors]: ../../contributors
