<?php

namespace LiveIntent\TelescopePrometheusExporter;

use Laravel\Telescope\Telescope;
use Prometheus\CollectorRegistry;
use Laravel\Telescope\IncomingEntry;

class TelescopePrometheusExporter
{
    /**
     * Register the configured exporters.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public static function start($app)
    {
        $registry = $app->make(CollectorRegistry::class);

        $exporters = collect(config('metrics.exporters'))
            ->filter(fn ($config) => $config['enabled'])
            ->map(fn ($config, $class) => new $class($registry, data_get($config, 'config', [])));

        Telescope::afterRecording(function (Telescope $instance, IncomingEntry $entry) use ($exporters) {
            rescue(fn () => $exporters->filter->shouldExport($entry)->each->export($entry));
        });
    }
}
