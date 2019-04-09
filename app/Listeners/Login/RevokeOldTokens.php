<?php

namespace App\Listeners\Login;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Events\AccessTokenCreated;

/**
 * 用于清除旧的access_token
 * Class RevokeOldTokens
 * @package App\Listeners
 */
class RevokeOldTokens
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
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {

        Log::info('event_accessToken: '.json_encode($event));
        DB::table('oauth_access_tokens')
            ->where('id', '!=', $event->tokenId)
            ->where('user_id', '=', $event->userId)
            ->where('client_id', '=', $event->clientId)
            ->where('created_at', '<', now()->toDateTimeString())
            ->where('revoked', '=', 0)
            ->delete();
    }
}
