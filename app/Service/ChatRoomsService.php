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
     * @param RoomOnlineUser $room_user
     * @param $room_id
     * @return bool
     */
    public function checkRooms(RoomOnlineUser $room_user, $room_id)
    {
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
}