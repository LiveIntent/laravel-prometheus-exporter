<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\IncomingEntry;
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
     * Register any additional things.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(JobProcessing::class, function () {
            $this->startTime = microtime(true);
        });

        $this->app['events']->listen(JobProcessed::class, function (JobProcessed $event) {
            $job = $event->job;

            $entry = IncomingEntry::make([
                'name' => $job->resolveName(),
                'attempts' => $job->attempts(),
                'status' => 'processed',
                'duration' => microtime(true) - $this->startTime
            ]);

            $this->export($entry);
        });

        $this->app['events']->listen(JobFailed::class, function (JobFailed $event) {
            $job = $event->job;

            $entry = IncomingEntry::make([
                'name' => $job->resolveName(),
                'attempts' => $job->attempts(),
                'status' => 'failed',
                'duration' => microtime(true) - $this->startTime
            ]);

            $this->export($entry);
        });
    }

    /**
     * Check if this exporter should export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return bool
     */
    public function shouldExport(IncomingEntry $entry)
    {
        return false;
    }

    /**
     * Export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return void
     */
    public function export(IncomingEntry $entry)
    {
        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'name' => $entry->content['name'],
            'attempts' => $entry->content['attempts'],
            'status' => $entry->content['status'],
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'job',
            'execution_duration_seconds',
            'The job execution duration recorded in seconds.',
            array_keys($labels),
            $this->config['buckets']
        );

        $histogram->observe(
            $entry->content['duration'],
            array_values($labels)
        );
    }
}
