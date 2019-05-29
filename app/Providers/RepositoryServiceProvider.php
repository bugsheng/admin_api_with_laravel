<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /*Users表相关*/
        $this->app->bind('App\Repositories\Interfaces\AdminAuthInterface', 'App\Repositories\AdminAuthRepository');
        $this->app->bind('App\Repositories\Interfaces\UserInterface', 'App\Repositories\UserRepository');


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
