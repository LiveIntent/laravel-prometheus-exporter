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
        $path = str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/';

        $shouldIgnore = collect(data_get($this->options, 'ignore_path_regex'))->some(
            fn ($pattern) => preg_match($pattern, $path)
        );

        if ($shouldIgnore) {
            return;
        }

        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'response_code' => strval($event->response->getStatusCode()),
            'method' => $event->request->method(),
            'path' => $event->request->route() ? $event->request()->route()->uri : null,
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'http',
            'request_memory_usage_bytes',
            'The memory usage of the request in bytes.',
            array_keys($labels),
            data_get($this->options, 'buckets')
        );

        $histogram->observe(
            memory_get_peak_usage(true),
            array_values($labels)
        );
    }
}
