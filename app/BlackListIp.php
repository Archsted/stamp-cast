<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlackListIp extends Model
{
    protected $fillable = [
        'user_id', 'ip'
    ];
}
