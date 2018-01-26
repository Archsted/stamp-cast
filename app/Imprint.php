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

    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function stamp()
    {
        return $this->belongsTo('App\Stamp');
    }

    public function scopeLatest($query)
    {
        $query->orderBy('created_at', 'desc');
    }
}
