<?php

namespace GeniussystemsNp\DynamicConfig\Providers;


use GeniussystemsNp\DynamicConfig\Repo\Eloquent\DynamicConfigDetailRepo;
use GeniussystemsNp\DynamicConfig\Repo\Eloquent\DynamicConfigRepo;
use GeniussystemsNp\DynamicConfig\Repo\RepoInterface\DynamicConfigDetailInterface;
use GeniussystemsNp\DynamicConfig\Repo\RepoInterface\DynamicConfigInterface;
use Illuminate\Support\ServiceProvider;

class DynamicConfigProvider extends ServiceProvider {
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public function boot() {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->app->bind(DynamicConfigInterface::class, DynamicConfigRepo::class);
        $this->app->bind(DynamicConfigDetailInterface::class, DynamicConfigDetailRepo::class);
    }

    public function loadRoutesFrom($path) {
        require $path;
    }
}
