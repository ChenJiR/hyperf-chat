<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $room_id
 * @property int $fd
 * @property string $entry_time
 *
 * @property User $user
 * @property Room $room
 */
class RoomOnlineUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'room_online_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'room_id' => 'integer', 'fd' => 'integer'];

    public function getUser()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function getRoom()
    {
        return $this->hasOne(Room::class, 'room_id', 'id');
    }
}