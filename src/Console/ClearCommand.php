<?php

namespace LiveIntent\TelescopePrometheusExporter\Console;

use Prometheus\Storage\Adapter;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
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
     * @return void
     */
    public function handle(Adapter $adapter)
    {
        $adapter->wipeStorage();

        $this->info('Metric data cleared!');
    }
}
