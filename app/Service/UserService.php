<?php


namespace App\Service;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Model\User;
use Hyperf\Di\Annotation\Inject;

/**
 * Class UserService
 * @package App\Service
 */
class UserService
{

    /**
     * @Inject()
     * @var ChatRoomsService
     */
    private $chatRoomService;

    /**
     * 登录
     * @param $roomid
     * @param $fd
     * @param $name
     * @param $password
     * @return User
     */
    public function login($fd, $name, $password, $roomid)
    {
        if (!$name || !$password) {
            throw new BusinessException(StatusCode::PARAMS_INVALID);
        }
        $user = User::loginOrSignup($name, $password);
        if ($this->chatRoomService->entryRooms($fd, $user, $roomid)) {
            return $user;
        } else {
            throw new BusinessException(StatusCode::SERVER_ERROR);
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
        return [];
    }
}