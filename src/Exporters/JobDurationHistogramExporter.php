<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class JobDurationHistogramExporter extends Exporter
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

        $this->app['events']->listen(JobProcessing::class, function () {
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
            'attempts' => strval($job->attempts()),
            'status' => $status,
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'job',
            'execution_duration_milliseconds',
            'The job execution duration recorded in milliseconds.',
            array_keys($labels),
            $this->options['buckets']
        );

        $duration = $this->startTime ? floor((microtime(true) - $this->startTime) * 1000) : null;

        $histogram->observe(
            $duration,
            array_values($labels)
        );
    }
}
