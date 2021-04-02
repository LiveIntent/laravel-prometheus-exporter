<?php

namespace LiveIntent\LaravelPrometheusExporter\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the metric exporter resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Metrics Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'metrics-config']);

        $this->info('Metrics scaffolding installed successfully.');
    }
}
