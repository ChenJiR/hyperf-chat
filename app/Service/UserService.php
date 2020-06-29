<?php


namespace App\Service;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Model\User;

/**
 * Class UserService
 * @package App\Service
 */
class UserService
{
    /**
     * 登录
     */
    public function login($roomid, $fd, $name, $password)
    {
        if ($name == "") {
            $name = '游客' . time();
        }
        if (!$name || !$password) {
            throw new BusinessException(StatusCode::PARAMS_INVALID);
        }
//        $user = User::query()->where

        $user = new ChatUser(array(
            'roomid' => $roomid,
            'fd' => $fd,
            'name' => htmlspecialchars($name)
        ));
        if (!$user->save()) {
            throw new Exception('This nick is in use.');
        }
    }

    /**
     * 登出
     */
    public function logout()
    {

    }

    /**
     * 获取在线用户列表
     */
    public function getOnlineUsers()
    {

    }
}