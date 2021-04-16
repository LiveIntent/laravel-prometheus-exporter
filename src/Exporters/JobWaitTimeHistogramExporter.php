<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Carbon\Carbon;
use Illuminate\Queue\Events\JobProcessing;

class JobWaitTimeHistogramExporter extends Exporter
{
    /**
     * Register the watcher.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(JobProcessing::class, [$this, 'export']);
    }

    /**
     * Export metrics.
     *
     * @param  \Illuminate\Queue\Events\JobProcessed|\Illuminate\Queue\Events\JobFailed  $event
     * @return void
     */
    public function export($event)
    {
        $job = $event->job;
        $pushedAt = data_get($job->payload(), 'pushedAt');

        if (!$pushedAt) {
            return;
        }

        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'name' => $job->resolveName(),
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'job',
            'wait_time_seconds',
            'The time the job had to wait before being processed recorded in seconds.',
            array_keys($labels),
            data_get($this->options, 'buckets')
        );

        $histogram->observe(
            Carbon::parse($pushedAt)->floatDiffInSeconds(),
            array_values($labels)
        );
    }
}
