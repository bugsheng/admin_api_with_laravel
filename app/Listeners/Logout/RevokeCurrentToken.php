<?php

namespace App\Listeners\Logout;

use App\Events\Logout;
use Illuminate\Support\Facades\DB;

class RevokeCurrentToken
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
        DB::table('oauth_access_tokens')->where('id', '=', $event->tokenId)->delete();
    }
}
