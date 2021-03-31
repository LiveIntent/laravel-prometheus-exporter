# This is my package LaravelPrometheusExporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/liveintent/laravel_prometheus_exporter.svg?style=flat-square)](https://packagist.org/packages/liveintent/laravel_prometheus_exporter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/liveintent/laravel_prometheus_exporter/run-tests?label=tests)](https://github.com/liveintent/laravel_prometheus_exporter/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/liveintent/laravel_prometheus_exporter/Check%20&%20fix%20styling?label=code%20style)](https://github.com/liveintent/laravel_prometheus_exporter/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/liveintent/laravel_prometheus_exporter.svg?style=flat-square)](https://packagist.org/packages/liveintent/laravel_prometheus_exporter)

[](delete) 1) manually replace `tam5, LiveIntent, auhor@domain.com, LiveIntent, liveintent, Vendor Name, laravel-prometheus-exporter, laravel_prometheus_exporter, laravel_prometheus_exporter, LaravelPrometheusExporter, This is my package LaravelPrometheusExporter` with their correct values
[](delete) in `CHANGELOG.md, LICENSE.md, README.md, ExampleTest.php, ModelFactory.php, LaravelPrometheusExporter.php, LaravelPrometheusExporterCommand.php, LaravelPrometheusExporterFacade.php, LaravelPrometheusExporterServiceProvider.php, TestCase.php, composer.json, create_laravel_prometheus_exporter_table.php.stub`
[](delete) and delete `configure-laravel_prometheus_exporter.sh`

[](delete) 2) You can also run `./configure-laravel_prometheus_exporter.sh` to do this automatically.

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/package-laravel_prometheus_exporter-laravel.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/package-laravel_prometheus_exporter-laravel)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require liveintent/laravel_prometheus_exporter
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="LiveIntent\LaravelPrometheusExporter\LaravelPrometheusExporterServiceProvider" --tag="laravel_prometheus_exporter-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="LiveIntent\LaravelPrometheusExporter\LaravelPrometheusExporterServiceProvider" --tag="laravel_prometheus_exporter-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel_prometheus_exporter = new LiveIntent\LaravelPrometheusExporter();
echo $laravel_prometheus_exporter->echoPhrase('Hello, Spatie!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [tam5](https://github.com/LiveIntent)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
