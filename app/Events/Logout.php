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
     * The ID of the user associated with the token.
     *
     * @var string
     */
    public $userId;

    /**
     * The ID of the client associated with the token.
     *
     * @var string
     */
    public $clientId;

    /**
     * Logout constructor.
     * @param $tokenId
     * @param $userId
     * @param $clientId
     */
    public function __construct($tokenId, $userId, $clientId)
    {

        $this->tokenId = $tokenId;

        $this->userId = $userId;

        $this->clientId = $clientId;
    }


}
