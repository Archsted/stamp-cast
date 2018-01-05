<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'id',
        'code',
        'user_id',
        'name',
        'description',
    ];
}
