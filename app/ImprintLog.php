<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImprintLog extends Model
{
    protected $fillable = [
        'imprint_id',
        'room_id',
        'stamp_id',
        'count',
    ];

    public function stamp()
    {
        return $this->belongsTo('App\Stamp');
    }
}
