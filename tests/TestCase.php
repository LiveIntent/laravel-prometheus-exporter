<?php

namespace LiveIntent\TelescopePrometheusExporter\Tests;

use LiveIntent\TelescopePrometheusExporter\TelescopePrometheusExporterServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            TelescopePrometheusExporterServiceProvider::class,
        ];
    }
}
