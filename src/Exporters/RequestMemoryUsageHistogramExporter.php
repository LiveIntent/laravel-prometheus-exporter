<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Illuminate\Foundation\Http\Events\RequestHandled;

class RequestMemoryUsageHistogramExporter extends Exporter
{
    /**
     * Register the watcher.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(RequestHandled::class, [$this, 'export']);
    }

    /**
     * Export metrics.
     *
     * @param  \Illuminate\Foundation\Http\Events\RequestHandled  $event
     * @return void
     */
    public function export($event)
    {
        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'code' => $event->response->getStatusCode(),
            'method' => $event->request->method(),
            'path' => str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/',
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'http',
            'request_memory_usage_megabytes',
            'The memory usage of the request in megabytes.',
            array_keys($labels),
            $this->options['buckets']
        );

        $memory =  round(memory_get_peak_usage(true) / 1024 / 1024, 1);

        $histogram->observe(
            $memory,
            array_values($labels)
        );
    }
}
