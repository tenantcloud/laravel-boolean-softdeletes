<?php

namespace Webkid\LaravelBooleanSoftdeletes;

use Illuminate\Support\ServiceProvider;
use Webkid\LaravelBooleanSoftdeletes\Commands\MigrateSoftDeletes;

class LaravelBooleanSoftdeletesServiceProvider extends ServiceProvider
{
	/**
	 * Perform post-registration booting of services.
	 */
	public function boot(): void
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				MigrateSoftDeletes::class,
			]);
		}
	}
}
