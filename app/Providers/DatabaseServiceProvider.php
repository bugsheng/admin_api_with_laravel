<?php


namespace App\Providers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        QueryBuilder::macro('sql', function () {
            $bindings = $this->getBindings();
            $sql = str_replace('?', '%s', $this->toSql());

            return sprintf($sql, ...$bindings);
        });

        EloquentBuilder::macro('sql', function () {
            $bindings = $this->getBindings();
            $sql = str_replace('?', '%s', $this->toSql());

            return sprintf($sql, ...$bindings);
        });
    }
}
