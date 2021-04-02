<?php

namespace LiveIntent\LaravelPrometheusExporter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use LiveIntent\LaravelPrometheusExporter\LaravelPrometheusExporterServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelPrometheusExporterServiceProvider::class,
        ];
    }
}
