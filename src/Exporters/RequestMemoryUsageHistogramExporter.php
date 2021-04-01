<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;

class RequestMemoryUsageHistogramExporter extends Exporter
{
    /**
     * Check if this exporter should export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return bool
     */
    public function shouldExport(IncomingEntry $entry)
    {
        return $entry->type === EntryType::REQUEST;
    }

    /**
     * Export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return void
     */
    public function export(IncomingEntry $entry)
    {
        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'code' => $entry->content['response_status'],
            'method' => $entry->content['method'],
            'path' => $entry->content['uri'],
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'http',
            'request_memory_usage_megabytes',
            'The memory usage of the request in megabytes.',
            array_keys($labels),
            $this->config['buckets']
        );

        $histogram->observe(
            $entry->content['memory'],
            array_values($labels)
        );
    }
}
