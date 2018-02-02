<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'name',
        'mime_type',
        'width',
        'height',
        'size',
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

    public function scopeAvailable($query, $roomId)
    {
        $query->where(function ($query) use ($roomId) {
            $query->where('room_id', $roomId)
                ->orWhereNull('room_id');
            });
    }

    public function room()
    {
        return $this->belongsTo('App\Room');
    }
}
