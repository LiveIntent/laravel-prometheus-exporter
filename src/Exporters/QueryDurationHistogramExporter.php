<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;

class QueryDurationHistogramExporter extends Exporter
{
    /**
     * Check if this exporter should export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return bool
     */
    public function shouldExport(IncomingEntry $entry)
    {
        return $entry->type === EntryType::QUERY;
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
            'sql' => $entry->content['sql'],
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'db',
            'query_duration_milliseconds',
            'The request duration recorded in milliseconds.',
            array_keys($labels),
            $this->config['buckets']
        );

        $histogram->observe(
            $entry->content['time'],
            array_values($labels)
        );
    }
}
