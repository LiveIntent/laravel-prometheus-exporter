<?php

namespace LiveIntent\TelescopePrometheusExporter;

use Laravel\Telescope\Telescope;
use Laravel\Telescope\IncomingEntry;
use Illuminate\Support\ServiceProvider;

class TelescopePrometheusExporterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Telescope::afterRecording(function (Telescope $instance, IncomingEntry $entry) {
            dd($entry);
        });
    }
}
