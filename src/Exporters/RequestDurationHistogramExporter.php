<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Illuminate\Foundation\Http\Events\RequestHandled;

class RequestDurationHistogramExporter extends Exporter
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
        $path =  str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/';

        $shouldIgnore = collect(data_get($this->options, 'ignore_path_regex'))->some(
            fn ($pattern) => preg_match($pattern, $path)
        );

        if ($shouldIgnore) return;

        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'response_code' => strval($event->response->getStatusCode()),
            'method' => $event->request->method(),
            'path' => $path
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'http',
            'request_duration_seconds',
            'The request duration recorded in seconds.',
            array_keys($labels),
            data_get($this->options, 'buckets')
        );

        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');
        $duration = $startTime ? (microtime(true) - $startTime) : null;

        $histogram->observe(
            $duration,
            array_values($labels)
        );
    }
}
