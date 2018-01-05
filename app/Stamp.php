<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    public function getNameAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
