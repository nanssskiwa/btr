<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $credits_left
 * @property int $credits_right
 * @property string $username
 * @property int $left_child
 * @property int $right_child
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class Tree extends Model
{
    protected $fillable = [
        'credits_left',
        'credits_right',
        'username',
        'left_child',
        'right_child',
        'value'
    ];
}
