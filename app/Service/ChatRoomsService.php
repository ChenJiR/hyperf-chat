<?php


namespace App\Service;

use App\Model\Room;
use App\Model\RoomOnlineUser;
use App\Model\User;
use Hyperf\Database\Model\Collection;

/**
 * Class UserService
 * @package App\Service
 */
class ChatRoomsService
{

    /**
     * 获取房间列表
     * @return Room[]
     */
    public function getRoomList()
    {
        return Room::all();
    }

    /**
     * 进入房间
     * @param int $fd
     * @param User $user
     * @param $room_id
     * @return bool
     */
    public function entryRooms(int $fd, User $user, $room_id)
    {
        $room_user = new RoomOnlineUser();
        $room_user->user_id = $user->id;
        $room_user->room_id = $room_id;
        $room_user->fd = $fd;
        return $room_user->save();
    }

    /**
     * 切换房间
     * @param $fd
     * @param $room_id
     * @return bool
     */
    public function checkRooms($fd, $room_id)
    {
        /** @var RoomOnlineUser $room_user */
        $room_user = RoomOnlineUser::query()->where(['fd' => $fd])->first();
        $room_user->room_id = $room_id;
        return $room_user->save();
    }

    /**
     * @param $room_id
     * @return Collection|RoomOnlineUser[]
     */
    public function getRoomUserList($room_id)
    {
        return RoomOnlineUser::query()->where(['room_id' => $room_id])->with('user')->get();
    }

    public function getUserByFd($fd)
    {
        /** @var RoomOnlineUser $room_user */
        $room_user = RoomOnlineUser::query()->where(['fd' => $fd])->first();
        return $room_user ? $room_user->user : null;
    }
}