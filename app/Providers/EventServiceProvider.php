<?php

namespace App\Providers;

use App\Events\Logout;
use App\Listeners\Logout\PruneCurrentToken;
use App\Listeners\Login\PruneOldTokens;
use App\Listeners\Logout\RevokeCurrentToken;
use App\Listeners\Login\RevokeOldTokens;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Events\RefreshTokenCreated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        //登录成功后的鉴权监听，移除旧token和refreshToken
        AccessTokenCreated::class => [
          RevokeOldTokens::class
        ],
        RefreshTokenCreated::class => [
           PruneOldTokens::class
        ],

        //退出登录事件监听，移除当前用户使用的token和refreshToken
        Logout::class => [
            RevokeCurrentToken::class,
            PruneCurrentToken::class
        ]

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
