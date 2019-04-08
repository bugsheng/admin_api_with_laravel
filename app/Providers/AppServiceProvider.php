<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * 当mysql数据库字符集为utf8mb4且版本低于8.0时
         * 需要将字段类型 varchar()最大存储长度从255更改为191
         * 否则migrate会报错，提示索引字段超长
         */
        Schema::defaultStringLength(191);

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
