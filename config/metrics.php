<?php

use LiveIntent\LaravelPrometheusExporter\Exporters;

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
            'prefix' => env('REDIS_METRICS_PREFIX', null),
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
    | exporter takes the data captured by Laravel and makes it available to
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
            'options' => [
                'buckets' => [
                    0.01, 0.02, 0.03, 0.04, 0.05, .06, 1, 1.2, 1.5, 1.8, 2, 3, 4, 5, 10
                ],
                'ignore_path_regex' => [
                    '/telescope/'
                ]
            ],
        ],

        Exporters\RequestMemoryUsageHistogramExporter::class => [
            'enabled' => env('EXPORT_REQUEST_MEMORY_USAGE_HISTOGRAM', true),
            'options' => [
                'buckets' => [
                    500000, 1000000, 2000000, 3000000, 4000000, 5000000, 6000000, 7000000, 8000000, 9000000, 10000000, 30000000
                ],
                'ignore_path_regex' => [
                    '/telescope/'
                ]
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Job Exporters
        |--------------------------------------------------------------------------
        */

        Exporters\JobProcessTimeHistogramExporter::class => [
            'enabled' => env('EXPORT_JOB_PROCESS_TIME_HISTOGRAM', true),
            'options' => [
                'buckets' => [
                    0.01, 0.02, 0.03, 0.04, 0.05, .06, 1, 1.2, 1.5, 1.8, 2, 3, 4, 5, 10
                ],
            ],
        ],

        Exporters\JobWaitTimeHistogramExporter::class => [
            'enabled' => env('EXPORT_JOB_WAIT_TIME_HISTOGRAM', true),
            'options' => [
                'buckets' => [
                    0.1, .3, .5, .7, 1, 2, 3, 4, 5, 10, 30, 40, 60,
                ],
                'cache' => [
                    'store' => 'redis',
                    'expiration' => now()->addDay()
                ]
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Query Exporters
        |--------------------------------------------------------------------------
        */

        Exporters\QueryDurationHistogramExporter::class => [
            'enabled' => env('EXPORT_QUERY_DURATION_HISTOGRAM', true),
            'options' => [
                'buckets' => [
                    .001, .005, 0.01, 0.02, 0.03, 0.04, 0.05, .06, 1, 1.2, 1.5, 1.8, 2, 3, 4, 5, 10
                ],
                'ignore_query_regex' => [
                    '/telescope_entries/',
                    '/create\s+table/',
                    '/alter\s+table/',
                    '/drop\s+table/',
                ]
            ],
        ],
    ],
];
