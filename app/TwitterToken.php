<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'secret',
    ];
}
