<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'name',
        'mime_type',
        'width',
        'height',
        'size',
    ];

    public function getNameAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
