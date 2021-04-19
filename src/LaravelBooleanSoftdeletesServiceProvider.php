<?php
namespace Webkid\LaravelBooleanSoftdeletes;

use Illuminate\Support\ServiceProvider;
use Webkid\LaravelBooleanSoftdeletes\Commands\MigrateSoftDeletes;

/**
 * Class LaravelBooleanSoftdeletesServiceProvider
 *
 * @package Webkid\LaravelBooleanSoftdeletes
 */
class LaravelBooleanSoftdeletesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateSoftDeletes::class
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
