<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    const IMPRINTER_LEVEL_ANYONE = 1;
    const IMPRINTER_LEVEL_USER_ONLY = 2;

    const UPLOADER_LEVEL_ANYONE = 1;
    const UPLOADER_LEVEL_USER_ONLY = 2;
    const UPLOADER_LEVEL_NOBODY = 9;

    const STAMP_COUNT_PER_PAGE = 30;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'description',
        'imprinter_level',
        'uploader_level',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function imprints()
    {
        return $this->hasMany('App\Imprint');
    }
}
