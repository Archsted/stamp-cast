<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorites extends Model
{
    public function stamp()
    {
        return $this->belongsTo('App\Stamp');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
