<?php

namespace Malico\Kassier;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class KassierServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kassier.php', 'kassier');
    }

    public function boot(): void
    {
        AboutCommand::add('Laravel Kassier', fn () => ['Version' => '0.X']);

        $this->registerMigrations();
        $this->registerPublishing();
    }

    /**
     * Register the package migrations.
     */
    protected function registerMigrations(): void
    {
        if (config('kassier.run_migrations', true) && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/kassier.php' => $this->app->configPath('kassier.php')], 'kassier-config');
            $this->publishes([__DIR__.'/../database/migrations' => $this->app->databasePath('migrations')], 'kassier-migrations');
        }
    }
}
