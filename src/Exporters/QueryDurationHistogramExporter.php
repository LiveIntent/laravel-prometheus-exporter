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
        $shouldIgnore = collect(data_get($this->options, 'ignore_query_regex'))->some(
            fn ($pattern) => preg_match($pattern, $event->sql)
        );

        if ($shouldIgnore) {
            return;
        }

        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'sql' => $event->sql,
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'db',
            'query_time_seconds',
            'The query time recorded in seconds.',
            array_keys($labels),
            data_get($this->options, 'buckets')
        );

        $histogram->observe(
            $event->time / 1000,
            array_values($labels)
        );
    }
}
