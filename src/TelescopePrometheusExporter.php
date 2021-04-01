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

        // Create all the enabled exporters
        $exporters = collect(config('metrics.exporters'))
            ->filter(fn ($config) => $config['enabled'])
            ->map(fn ($config, $class) => new $class($app, $registry, data_get($config, 'config', [])));

        // Exporters may need a chance to set things up
        $exporters->each(function ($exporter) {
            if (method_exists($exporter, 'register')) {
                $exporter->register();
            }
        });

        // Hook into Telescope and register exporters
        Telescope::afterRecording(function (Telescope $_, IncomingEntry $entry) use ($exporters) {
            rescue(fn () => $exporters->filter->shouldExport($entry)->each->export($entry));
        });
    }
}
