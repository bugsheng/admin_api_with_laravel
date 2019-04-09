<?php

namespace App\Listeners\Logout;

use App\Events\Logout;
use Illuminate\Support\Facades\DB;

class PruneCurrentToken
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
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        DB::table('oauth_refresh_tokens')->where('access_token_id', '=', $event->tokenId)->delete();
    }
}
