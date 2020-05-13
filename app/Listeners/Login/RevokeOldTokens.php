<?php

namespace App\Listeners\Login;

use DB;
use Laravel\Passport\Events\AccessTokenCreated;

/**
 * 用于清除旧的access_token
 * Class RevokeOldTokens
 *
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
     * @param  AccessTokenCreated $event
     *
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        //登录成功删除该用户的其他token
        DB::table('oauth_access_tokens')
            ->where('id', '!=', $event->tokenId)
            ->where('user_id', '=', $event->userId)
            ->where('client_id', '=', $event->clientId)
            ->where('created_at', '<', now()->toDateTimeString())
            ->delete();
    }
}
