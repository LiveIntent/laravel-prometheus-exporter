<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Prometheus\CollectorRegistry;
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
     * The exporter options.
     *
     * @var array
     */
    protected $options;

    /**
     * Create a new exporter.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Prometheus\CollectorRegistry  $registry
     * @param  array  $config
     * @return void
     */
    public function __construct(Application $app, CollectorRegistry $registry, array $options = [])
    {
        $this->app = $app;
        $this->registry = $registry;
        $this->options = $options;
    }

    /**
     * Register the watcher.
     *
     * @return void
     */
    abstract public function register();

    /**
     * Export metrics.
     *
     * @param mixed  $event
     * @return void
     */
    abstract public function export($event);
}
