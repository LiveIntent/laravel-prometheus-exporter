<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class JobProcessTimeHistogramExporter extends Exporter
{
    /**
     * The time the job started processing.
     *
     * @var float
     */
    private $startTime;

    /**
     * Register the watcher.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(JobProcessing::class, function ($e) {
            $this->startTime = microtime(true);
        });

        $this->app['events']->listen(JobProcessed::class, [$this, 'export']);
        $this->app['events']->listen(JobFailed::class, [$this, 'export']);
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
        $status = is_a($event, JobProcessed::class) ? 'processed' : 'failed';

        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'name' => $job->resolveName(),
            'status' => $status,
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'job',
            'process_time_seconds',
            'The job processing time recorded in seconds.',
            array_keys($labels),
            data_get($this->options, 'buckets')
        );

        $duration = $this->startTime ? (microtime(true) - $this->startTime) : null;

        $histogram->observe(
            $duration,
            array_values($labels)
        );
    }
}
