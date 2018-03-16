<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'text',
        'ip',
    ];

    protected $hidden = [
        'ip',
        'created_at',
        'updated_at',
        'pivot',
    ];

    public function stamps()
    {
        return $this->belongsToMany('App\Stamp')->withTimestamps();
    }

    public function stampTags()
    {
        return $this->hasMany('App\StampTag');
    }
}
