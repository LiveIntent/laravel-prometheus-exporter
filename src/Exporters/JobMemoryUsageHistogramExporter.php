<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;

class JobMemoryUsageHistogramExporter extends Exporter
{
    /**
     * Check if this exporter should export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return bool
     */
    public function shouldExport(IncomingEntry $entry)
    {
        return $entry->type === EntryType::JOB;
    }

    /**
     * Export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return void
     */
    public function export(IncomingEntry $entry)
    {
        // dump($entry);
        // $app['events']->listen(JobProcessed::class, [$this, 'recordProcessedJob']);
        // dd($entry);
        // $labels = [
        //     'service' => config('app.name'),
        //     'environment' => config('app.env'),
        //     'code' => $entry->content['response_status'],
        //     'method' => $entry->content['method'],
        //     'path' => $entry->content['uri'],
        // ];

        // $histogram = $this->registry->getOrRegisterHistogram(
        //     'http',
        //     'request_duration_milliseconds',
        //     'The request duration recorded in milliseconds.',
        //     array_keys($labels),
        //     $this->config['buckets']
        // );

        // $histogram->observe(
        //     $entry->content['duration'],
        //     array_values($labels)
        // );
    }
}
