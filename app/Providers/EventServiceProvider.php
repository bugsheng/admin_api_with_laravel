<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Registered' => [
            'Illuminate\Auth\Listeners\SendEmailVerificationNotification'
        ],

        //登录成功后的鉴权监听，移除旧token和refreshToken
        'Laravel\Passport\Events\AccessTokenCreated' => [
            'App\Listeners\Login\RevokeOldTokens'
        ],
//        'Laravel\Passport\Events\RefreshTokenCreated' => [
//            'App\Listeners\Login\PruneOldTokens'
//        ],

        //退出登录事件监听，移除当前用户使用的token和refreshToken
        'App\Events\Logout' => [
            'App\Listeners\Logout\RevokeCurrentToken',
            'App\Listeners\Logout\PruneCurrentToken'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
