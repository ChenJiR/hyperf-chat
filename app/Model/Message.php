<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property string $content
 * @property string $create_time
 * @property string $update_time
 */
class Message extends Model
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'message';
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
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'type' => 'integer'];
}