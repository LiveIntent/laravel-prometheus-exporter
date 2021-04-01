<?php

namespace LiveIntent\TelescopePrometheusExporter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use LiveIntent\TelescopePrometheusExporter\TelescopePrometheusExporterServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            TelescopePrometheusExporterServiceProvider::class,
        ];
    }
}
