<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imprint extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'stamp_id',
        'comment',
        'ip',
    ];
}
