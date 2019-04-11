<?php

namespace App\Events;

/**
 * 退出登录事件
 * Class Logout
 * @package App\Events
 */
class Logout
{

    /**
     * The current token ID.
     *
     * @var string
     */
    public $tokenId;

    /**
     * Logout constructor.
     * @param $tokenId
     */
    public function __construct($tokenId)
    {

        $this->tokenId = $tokenId;

    }


}
