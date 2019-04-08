<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Events\RefreshTokenCreated;

/**
 * 用于清除旧的refresh_token
 * Class PruneOldTokens
 * @package App\Listeners
 */
class PruneOldTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RefreshTokenCreated  $event
     * @return void
     */
    public function handle(RefreshTokenCreated $event)
    {
        Log::info('event_refreshToken: '.json_encode($event));
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', '<>', $event->accessTokenId)
            ->where('revoked', '=', 0)
            ->delete();
    }
}
