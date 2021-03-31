# Laravel Telescope Prometheus Exporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/liveintent/laravel_prometheus_exporter.svg?style=flat-square)](https://packagist.org/packages/liveintent/laravel_prometheus_exporter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/liveintent/laravel_prometheus_exporter/run-tests?label=tests)](https://github.com/liveintent/laravel_prometheus_exporter/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/liveintent/laravel_prometheus_exporter/Check%20&%20fix%20styling?label=code%20style)](https://github.com/liveintent/laravel_prometheus_exporter/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/liveintent/laravel_prometheus_exporter.svg?style=flat-square)](https://packagist.org/packages/liveintent/laravel_prometheus_exporter)


This is a Laravel package that collects data via [Telescope](https://laravel.com/docs/8.x/telescope) and exposes a `/metrics` endpoint in your application which can then be scraped by [Prometheus](https://prometheus.io/).

For a full list of exported metrics, see [exporters](#exporters).

## Installation

You can install the package via composer:

```bash
composer require liveintent/telescope-prometheus-exporter
```

The package will auto-register itself, and a `metrics` config file will be added to your project. If you need to copy the config file manually, you may run:

```bash
php artisan metrics:install
```

## Usage

The `metrics` config file contains a list of enabled exporters. Each exporter will collect data and store it in your configured data store.

### Storage

The storage drivers currently supperted are redis, apc, and in-memory. You may adjust this value by setting the env `METRICS_STORAGE_DRIVER`.

You will need the appropriate `pecl` extension installed ([apc](https://pecl.php.net/package/APCU) or [php-redis](https://pecl.php.net/package/redis)).

### Exporters

#### Request Duration Historam Exporter - `http_request_duration_milliseconds_bucket`

This will export histogram data for request duration. 

##### Example

```php
Exporters\RequestDurationHistogramExporter::class => [
    'enabled' => env('EXPORT_REQUEST_DURATION_HISTOGRAM', true),
    'config' => [
        'buckets' => [
            5, 10, 15, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200
        ],
    ],
],
```

##### Labels

| name        | description            | example            |
|:------------|:-----------------------|--------------------|
| service     | name of the service    | my-amazing-api     |
| environment | the environment        | qa, prod, etc      |
| code        | the http response code | 200, 400, etc      |
| method      | the http method        | GET, POST, etc     |
| path        | the uri of the request | '/', '/posts', etc |

### Writing New Exporters

is easy, will explain
