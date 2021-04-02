<?php

namespace LiveIntent\LaravelPrometheusExporter\Console;

use Illuminate\Console\Command;
use Prometheus\Storage\Adapter;
use Illuminate\Console\ConfirmableTrait;

class ClearCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all metric data from storage';

    /**
     * Execute the console command.
     *
     * @param  \Prometheus\Storage\Adapter  $adapter
     * @return int|null
     */
    public function handle(Adapter $adapter)
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $adapter->wipeStorage();

        $this->info('Metric data cleared!');
    }
}
