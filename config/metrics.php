<?php

use LiveIntent\TelescopePrometheusExporter\Exporters;

return [

    /*
    |--------------------------------------------------------------------------
    | Metrics Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where the metrics will be accessible from. If the
    | setting is null, the metrics will reside under the same domain as the
    | application. Otherwise, this value would be used for the subdomain.
    |
    */

    'domain' => env('METRICS_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Metrics Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where the metrics will be accessible from. You may
    | change this path to anything you like, but by default Prometheus will
    | be looking for '/metrics', so you probably don't want to change it.
    |
    */

    'path' => env('METRICS_PATH', 'metrics'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the storage driver that'll be used
    | to store metric data. In most cases, you will want something that can
    | persist state across multiple Laravel instances and is fast enough.
    |
    | Supported drivers: "apc", "redis", "memory"
    */

    'driver' => env('METRICS_STORAGE_DRIVER', 'memory'),

    'storage' => [
        'redis' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', '6379'),
            'password' => env('REDIS_PASSWORD', null),
            'timeout' => 0.1,
            'read_timeout' => '10',
            'persistent_connections' => null,
            'prefix' => env('REDIS_METRICS_PREFIX', null)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Exporter Primary Switch
    |--------------------------------------------------------------------------
    |
    | This primary switch may be used as an override to turn off every metric
    | exporter regardless of their individual configuration, which is meant
    | to be a convenient way to enable or disable metric data collection.
    |
    */

    'enabled' => env('METRICS_EXPORTER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Metric Exporters
    |--------------------------------------------------------------------------
    |
    | The following array lists the "exporters" that will be registered. Each
    | exporter takes data collected by Telescope and makes it available for
    | the metrics endpoint to expose. Prometheus can then store the data.
    |
    | You may also define additional custom exporters and add them below.
    |
    */

    'exporters' => [

        /*
        |--------------------------------------------------------------------------
        | HTTP Request Exporters
        |--------------------------------------------------------------------------
        */

        Exporters\RequestDurationHistogramExporter::class => [
            'enabled' => env('EXPORT_REQUEST_DURATION_HISTOGRAM', true),
            'config' => [
                'buckets' => [
                    5, 10, 15, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200
                ]
            ]
        ],

        Exporters\RequestMemoryUsageHistogramExporter::class => [
            'enabled' => env('EXPORT_REQUEST_MEMORY_USAGE_HISTOGRAM', true),
            'config' => [
                'buckets' => [
                    0.1, 0.2, 0.3, 0.4, 0.5, 1, 5, 10, 20, 50, 100, 500
                ]
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Job Exporters
        |--------------------------------------------------------------------------
        */

        Exporters\JobDurationHistogramExporter::class => [
            'enabled' => env('EXPORT_JOB_DURATION_HISTOGRAM', true),
            'config' => [
                'buckets' => [
                    0.1, 0.2, 0.3, 0.4, 0.5, 1, 5, 10, 20, 50, 100, 500
                ]
            ]
        ],

        /*
        |--------------------------------------------------------------------------
        | Query Exporters
        |--------------------------------------------------------------------------
        */

        Exporters\QueryDurationHistogramExporter::class => [
            'enabled' => env('EXPORT_QUERY_DURATION_HISTOGRAM', true),
            'config' => [
                'buckets' => [
                    0.1, 0.2, 0.3, 0.4, 0.5, 1, 5, 10, 20, 50, 100, 500
                ]
            ]
        ],

    ],
];
