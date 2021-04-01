# Laravel Telescope Prometheus Exporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/liveintent/telescope-prometheus-exporter.svg?style=flat-square)](https://packagist.org/packages/liveintent/telescope-prometheus-exporter)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/liveintent/telescope-prometheus-exporter/run-tests?label=tests)](https://github.com/liveintent/telescope-prometheus-exporter/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/liveintent/telescope-prometheus-exporter/Check%20&%20fix%20styling?label=code%20style)](https://github.com/liveintent/telescope-prometheus-exporter/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/liveintent/telescope-prometheus-exporter.svg?style=flat-square)](https://packagist.org/packages/liveintent/telescope-prometheus-exporter)


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

If you need to clear the storage, you may do so with:

```bash
php artisan metrics:clear
```

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

#### Request Memory Usage Historam Exporter - `http_request_memory_usage_megabytes`

This will export histogram data for request memory usage. 

##### Example

```php
Exporters\RequestMemoryUsageHistogramExporter::class => [
    'enabled' => env('EXPORT_MEMORY_USAGE_HISTOGRAM', true),
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

#### Job Duration Historam Exporter - `job_execution_duration_milliseconds_bucket`

This will export histogram data for job execution duration. 

##### Example

```php
Exporters\JobDurationHistogramExporter::class => [
    'enabled' => env('EXPORT_JOB_DURATION_HISTOGRAM', true),
    'config' => [
        'buckets' => [
            5, 10, 15, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200
        ],
    ],
],
```

##### Labels

| name        | description                                | example                   |
|:------------|:-------------------------------------------|---------------------------|
| service     | name of the service                        | my-amazing-api            |
| environment | the environment                            | qa, prod, etc             |
| name        | the classname of the job                   | App\\Jobs\\ProcessPayment |
| attempts    | the number of times the job has been tried | 1                         |
| status      | the job status                             | procesed, failed          |

#### Query Duration Historam Exporter - `db_query_duration_milliseconds_bucket`

This will export histogram data for query execution times. 

##### Example

```php
Exporters\QueryDurationHistogramExporter::class => [
    'enabled' => env('EXPORT_QUERY_DURATION_HISTOGRAM', true),
    'config' => [
        'buckets' => [
            5, 10, 15, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200
        ],
    ],
],
```

##### Labels

| name        | description         | example               |
|:------------|:--------------------|-----------------------|
| service     | name of the service | my-amazing-api        |
| environment | the environment     | qa, prod, etc         |
| sql         | the sql query       | SELECT * FROM `users` |

### Writing New Exporters

You may also write your own exporter. You only need to implement two methods, `shouldExport` and `export`.

The following example increments a counter every time the `/` route is visited.

```php
<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;

class HomePageVisitsCountExporter extends Exporter
{
    /**
     * Check if this exporter should export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return bool
     */
    public function shouldExport(IncomingEntry $entry)
    {
        return $entry->type === EntryType::REQUEST && $entry->content['uri'] === '/';
    }

    /**
     * Export metrics for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return void
     */
    public function export(IncomingEntry $entry)
    {
        $counter = $this->registry->getOrRegisterCounter(
            '', 'home_page_visits_counter', 'it is a silly example'
        );

        $counter->inc();
    }
}
```

Register your Exporter class by placing it in the list of exporters found in the `metrics` config file.

For more advanced usage, or if you need to hook into an event that Telescope does not record, you may implement the `register` method on the exporter which will be called during application startup.
