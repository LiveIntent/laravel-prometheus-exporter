<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Prometheus\CollectorRegistry;
use Laravel\Telescope\IncomingEntry;
use Illuminate\Contracts\Foundation\Application;

abstract class Exporter
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The prometheus collector registry.
     *
     * @var \Prometheus\CollectorRegistry
     */
    protected $registry;

    /**
     * The exporter config.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new exporter.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Prometheus\CollectorRegistry  $registry
     * @param  array|null  $config
     * @return void
     */
    public function __construct(Application $app, CollectorRegistry $registry, array $config = [])
    {
        $this->app = $app;
        $this->registry = $registry;
        $this->config = $config;
    }

    /**
     * Check if this exporter should export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return bool
     */
    abstract public function shouldExport(IncomingEntry $entry);

    /**
     * Export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return void
     */
    abstract public function export(IncomingEntry $entry);
}
