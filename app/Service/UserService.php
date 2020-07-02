<?php


namespace App\Service;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Model\RoomOnlineUser;
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
     * @param int $fd
     * @return int|mixed
     */
    public function logout(int $fd)
    {
        return RoomOnlineUser::query()->where('fd', '=', $fd)->delete();
    }

    /**
     * 获取在线用户列表
     */
    public function getOnlineUsers()
    {
        $online_user = [];
        foreach (RoomOnlineUser::query()->with('user')->get() as $item) {
            /** @var RoomOnlineUser $item $item */
            $user = [
                'avatar' => '',
                'fd' => $item->fd,
                'name' => $item->user->username,
                'roomid' => $item->room_id,
                'time' => date("H:i", strtotime($item->entry_time))
            ];

            isset($online_user[$item->room->id])
                ? $online_user[$item->room->id][] = $user : $online_user[$item->room->id] = [$user];
        }
        return $online_user;
    }
}