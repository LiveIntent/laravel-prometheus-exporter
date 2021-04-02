<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Illuminate\Database\Events\QueryExecuted;

class QueryDurationHistogramExporter extends Exporter
{
    /**
     * Register the watcher.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(QueryExecuted::class, [$this, 'export']);
    }

    /**
     * Export metrics.
     *
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return void
     */
    public function export($event)
    {
        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'sql' => $event->sql,
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'db',
            'query_duration_milliseconds',
            'The request duration recorded in milliseconds.',
            array_keys($labels),
            $this->options['buckets']
        );

        $histogram->observe(
            floatval(number_format($event->time, 2, '.', '')),
            array_values($labels)
        );
    }
}
