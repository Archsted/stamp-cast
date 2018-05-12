<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function stamps()
    {
        return $this->belongsToMany('App\Stamp')->withPivot('order')->withTimestamps();
    }

    // 直近で追加されたスタンプ
    public function latestStamp()
    {
        return $this->belongsToMany('App\Stamp')
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
