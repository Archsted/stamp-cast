<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    protected $hidden = [
        'pivot',
        'ip',
    ];

    protected $fillable = [
        'user_id',
        'room_id',
        'name',
        'mime_type',
        'width',
        'height',
        'size',
        'is_animation',
        'hash',
        'ip',
        'thumbnail',
    ];

    /**
     * 論理削除されているものを除外するグローバルスコープ
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('softDelete', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }

    public function getNameAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function getThumbnailAttribute($value)
    {
        if (is_null($value)) {
            return $value;
        } else {
            return asset('storage/' . $value);
        }
    }

    public function scopeAvailable($query, $roomId)
    {
        $query->where(function ($query) use ($roomId) {
            $query->where('room_id', $roomId)
                ->orWhereNull('room_id');
            });
    }

    public function scopeWithoutBlackList($query, $blackListIps)
    {
        $query->where(function ($query) use ($blackListIps) {
            $query
                ->whereNotIn('ip', $blackListIps)
                ->orWhereNull('ip');
        });
    }

    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    public function books()
    {
        return $this->belongsToMany('App\Book')->withPivot('order')->withTimestamps();
    }
}
