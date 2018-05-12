<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StampTag extends Model
{
    protected $table = 'stamp_tag';

    public function tag()
    {
        return $this->belongsTo('App\Tag');
    }

    public function stamp()
    {
        return $this->belongsTo('App\Stamp');
    }
}
