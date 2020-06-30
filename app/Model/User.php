<?php

declare (strict_types=1);

namespace App\Model;

use App\Constants\StatusCode;
use App\Exception\BusinessException;

/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $create_time
 * @property string $update_time
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';
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
    protected $casts = ['id' => 'integer'];

    /**
     * @param $username
     * @param $password
     * @return User|bool
     */
    public static function signUp($username, $password)
    {
        $user = new User();
        $user->username = $username;
        $user->password = $password;
        return $user->save() ? $user : false;
    }

    /**
     * @param $username
     * @param $password
     * @return User
     */
    public static function loginOrSignup($username, $password)
    {
        $user = self::query()->where(['username' => $username])->firstOr(
            ['*'],
            function () use ($username, $password) {
                return self::signUp($username, $password);
            }
        );
        if (!$user || $user->password != $password) {
            throw new BusinessException(StatusCode::PASSWORD_ERROR);
        }
        return $user;
    }

}