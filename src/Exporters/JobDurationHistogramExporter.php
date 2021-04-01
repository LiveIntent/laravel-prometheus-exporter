<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Contracts\Queue\Job;

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

        // dump('register');
        // $content = [
        //     'status' => 'processed',
        //     'connection' => $connection,
        //     'queue' => $queue,
        //     'name' => $payload['displayName'],
        //     'tries' => $payload['maxTries'],
        //     'timeout' => $payload['timeout'],
        // ]
        $this->app['events']->listen(JobProcessed::class, function (JobProcessed $event) {
            echo "The job was processed\n";
            // dump($event->job->getConnectionName());
            $entry = IncomingEntry::make([
                'status' => 'processed',
                'name' => 'test',
                'tries' => 2
            ]);

            $this->export($entry);
        });

        // $this->app['events']->listen(JobFailed::class, function () {
        //     echo "The job was failed\n";
        // });

        // $app['events']->listen(JobFailed::class, [$this, 'recordFailedJob']);
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
        dump($entry);

        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'name' => $entry->content['name'],
            'tries' => $entry->content['tries'],
            'status' => $entry->content['status'],
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'job',
            'execution_duration_seconds',
            'The job execution duration recorded in seconds.',
            array_keys($labels),
            $this->config['buckets']
        );

        dump($this->startTime);
        // if (!$this->startTime) {
        //     return;
        // }

        // $duration = microtime(true) - $this->startTime;
        // dump($duration);

        $histogram->observe(
            1,
            array_values($labels)
        );
    }
}
