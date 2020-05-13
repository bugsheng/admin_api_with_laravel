<?php

namespace App\Listeners\Login;

use DB;
use Laravel\Passport\Events\RefreshTokenCreated;

/**
 * 用于清除旧的refresh_token
 * Class PruneOldTokens
 *
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
     * @param  RefreshTokenCreated $event
     *
     * @return void
     */
    public function handle(RefreshTokenCreated $event)
    {
        //登录成功删除其他refreshToken， 该逻辑好像有点问题，会把其他所有人的refresh_token都删除了
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', '!=', $event->accessTokenId)
            ->delete();
    }
}
