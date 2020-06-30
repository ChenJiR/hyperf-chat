<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $create_time
 * @property string $update_time
 */
class Room extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'room';
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
}