<?php

namespace LiveIntent\TelescopePrometheusExporter;

use Prometheus\Storage\APC;
use Prometheus\Storage\Redis;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\InMemory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use LiveIntent\TelescopePrometheusExporter\Http\Controllers\MetricsController;

class TelescopePrometheusExporterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if (! config('metrics.enabled')) {
            return;
        }

        $this->registerRoute();
        $this->registerPublishing();

        TelescopePrometheusExporter::start($this->app);
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoute()
    {
        $config = [
            'domain' => config('metrics.domain', null),
            'prefix' => config('metrics.path'),
        ];

        Route::group($config, function () {
            Route::get('', MetricsController::class);
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/metrics.php' => config_path('metrics.php'),
            ], 'metrics-config');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/metrics.php',
            'metrics'
        );

        $this->registerStorageDriver();

        $this->commands([
            Console\ClearCommand::class,
            Console\InstallCommand::class,
        ]);
    }

    /**
     * Register the package storage driver.
     *
     * @return void
     */
    protected function registerStorageDriver()
    {
        $driver = config('metrics.driver');

        if (method_exists($this, $method = 'register'.ucfirst($driver).'Driver')) {
            $this->$method();
        }
    }

    /**
     * Register the package redis storage driver.
     *
     * @return void
     */
    protected function registerRedisDriver()
    {
        $this->app->singleton(Adapter::class, function () {
            $redis = new Redis(config('metrics.storage.redis'));

            if (config('metrics.storage.redis.prefix')) {
                $redis->setPrefix(config('metrics.storage.redis.prefix'));
            }

            return $redis;
        });
    }

    /**
     * Register the package apc storage driver.
     *
     * @return void
     */
    protected function registerApcDriver()
    {
        $this->app->singleton(
            Adapter::class,
            APC::class
        );
    }

    /**
     * Register the package in memory storage driver.
     *
     * @return void
     */
    protected function registerMemoryDriver()
    {
        $this->app->singleton(
            Adapter::class,
            InMemory::class
        );
    }
}
