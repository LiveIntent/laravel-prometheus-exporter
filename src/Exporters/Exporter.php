<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\IncomingEntry;
use Prometheus\CollectorRegistry;

abstract class Exporter
{
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
     * @param \Prometheus\CollectorRegistry  $registry
     * @return void
     */
    public function __construct(CollectorRegistry $registry, array $config = [])
    {
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
