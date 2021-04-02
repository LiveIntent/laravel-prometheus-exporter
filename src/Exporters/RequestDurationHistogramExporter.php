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
        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'code' => $event->response->getStatusCode(),
            'method' => $event->request->method(),
            'path' => str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/',
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'http',
            'request_duration_milliseconds',
            'The request duration recorded in milliseconds.',
            array_keys($labels),
            $this->options['buckets']
        );

        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');
        $duration = $startTime ? floor((microtime(true) - $startTime) * 1000) : null;

        $histogram->observe(
            $duration,
            array_values($labels)
        );
    }
}
