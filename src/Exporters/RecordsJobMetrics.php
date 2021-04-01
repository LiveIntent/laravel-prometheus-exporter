<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Illuminate\Contracts\Queue\Job;
use Laravel\Telescope\IncomingEntry;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

trait RecordsJobMetrics
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
     * @psalm-suppress UndefinedInterfaceMethod
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen(JobProcessing::class, function () {
            $this->startTime = microtime(true);
        });

        $this->app['events']->listen(JobProcessed::class, function (JobProcessed $event) {
            $this->export($this->makeEntry($event->job, 'processed'));
        });

        $this->app['events']->listen(JobFailed::class, function (JobFailed $event) {
            $this->export($this->makeEntry($event->job, 'failed'));
        });
    }

    /**
     * Make the entry for exporting.
     *
     * @param \Illuminate\Contracts\Queue\Job  $job
     * @param string  $status
     * @return \Laravel\Telescope\IncomingEntry
     */
    private function makeEntry(Job $job, $status)
    {
        return IncomingEntry::make([
            'name' => $job->resolveName(),
            'attempts' => $job->attempts(),
            'status' => $status,
            'duration' => $this->startTime ? floor((microtime(true) - $this->startTime) * 1000) : null,
            'memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 1),
        ]);
    }
}
