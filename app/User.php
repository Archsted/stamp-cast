<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login_id', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function room()
    {
        return $this->hasOne('App\Room');
    }

    /**
     * お気に入りのスタンプ一覧
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany('App\Stamp', 'favorites')->withTimestamps();
    }

    public function stamps()
    {
        return $this->hasMany('App\Stamp');
    }

    public function blackListUsers()
    {
        return $this->belongsToMany(
            'App\User',
            'black_list_users',
            'user_id',
            'target_id')
            ->withTimestamps();
    }

    public function blackListIps()
    {
        return $this->hasMany('App\BlackListIp');
    }
}
