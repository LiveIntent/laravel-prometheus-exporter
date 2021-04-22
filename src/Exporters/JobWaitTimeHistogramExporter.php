<?php

namespace LiveIntent\LaravelPrometheusExporter\Exporters;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Bus\BatchRepository;
use Illuminate\Queue\Events\JobProcessing;

class JobWaitTimeHistogramExporter extends Exporter
{
    /**
     * Register the watcher.
     */
    public function register()
    {
        $this->app['events']->listen(JobProcessing::class, [$this, 'export']);
    }

    /**
     * Export metrics.
     *
     * @param \Illuminate\Queue\Events\JobFailed|\Illuminate\Queue\Events\JobProcessed $event
     */
    public function export($event)
    {
        // Normal jobs that are dispatched through Horizon will have a
        // `pushedAt` timestamp that we can use for our caclulations
        $job = $event->job;
        $pushedAt = data_get($job->payload(), 'pushedAt');

        // Batched jobs unfortunately do not contain their own pushedAt
        // timestamp, but the batch itself does have a timestamp for us
        if (!$pushedAt) {
            $batch = $this->getBatch($job);
            $pushedAt = data_get($batch, 'createdAt');
        }

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

    /**
     * Get the encompassing batch of a job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return \Illuminate\Bus\Batch|null
     */
    private function getBatch(Job $job)
    {
        return rescue(function () use ($job) {
            $json = json_decode($job->getRawBody());
            $instance = unserialize(data_get($json, 'data.command'));
            $batchId = data_get($instance, 'batchId');

            if ($batchId) {
                return resolve(BatchRepository::class)->find($batchId);
            }
        });
    }
}
