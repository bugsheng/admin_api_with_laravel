<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\Interfaces\AuthInterface', 'App\Services\AuthService');
        $this->app->bind('App\Services\Interfaces\PersonnelInterface', 'App\Services\PersonnelService');
        $this->app->bind('App\Services\Interfaces\StorageInterface', 'App\Services\StorageService');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}