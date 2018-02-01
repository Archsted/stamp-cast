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

    public function scopeAvailable($query, $roomId)
    {
        $query->where(function ($query) use ($roomId) {
            $query->where('room_id', $roomId)
                ->orWhereNull('room_id');
            });
    }
}
