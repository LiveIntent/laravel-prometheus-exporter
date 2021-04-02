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
            'sql' => $this->replaceBindings($event),
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

    /**
     * Format the given bindings to strings.
     *
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return array
     */
    protected function formatBindings($event)
    {
        return $event->connection->prepareBindings($event->bindings);
    }

    /**
     * Replace the placeholders with the actual bindings.
     *
     * @psalm-suppress InvalidScalarArgument
     * @param  \Illuminate\Database\Events\QueryExecuted  $event
     * @return string
     */
    protected function replaceBindings($event)
    {
        $sql = $event->sql;

        foreach ($this->formatBindings($event) as $key => $binding) {
            $regex = is_numeric($key)
                ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            if ($binding === null) {
                $binding = 'null';
            } elseif (! is_int($binding) && ! is_float($binding)) {
                $binding = $event->connection->getPdo()->quote($binding);
            }

            $sql = preg_replace($regex, $binding, $sql, 1);
        }

        return $sql;
    }
}
